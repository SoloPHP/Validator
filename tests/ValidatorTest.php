<?php

declare(strict_types=1);

namespace Solo\Validator\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Solo\Validator\Field;
use Solo\Validator\InvalidRuleException;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\PresenceRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Rules\LengthRule;
use Solo\Validator\Validator;

final class ValidatorTest extends TestCase
{
    public function testValidDataHasNoErrors(): void
    {
        $errors = (new Validator())->validate(
            ['email' => 'user@example.com', 'age' => '42'],
            ['email' => 'required|email', 'age' => 'integer|min_value:18'],
        );

        $this->assertSame([], $errors);
    }

    public function testErrorsAreKeyedByFieldInRuleOrder(): void
    {
        $errors = (new Validator())->validate(
            ['password' => '123', 'email' => 'invalid'],
            ['email' => 'required|email', 'password' => 'required|min:8'],
        );

        $this->assertSame([
            'email' => [['rule' => 'email']],
            'password' => [['rule' => 'min', 'params' => ['8']]],
        ], $errors);
    }

    public function testWhitespaceAroundRulesIsIgnored(): void
    {
        $errors = (new Validator())->validate(['email' => 'invalid'], ['email' => ' required | email ']);

        $this->assertSame(['email' => [['rule' => 'email']]], $errors);
    }

    public function testEmptySegmentsAreSkipped(): void
    {
        $errors = (new Validator())->validate(['name' => ''], ['name' => '|required||']);

        $this->assertSame(['name' => [['rule' => 'required']]], $errors);
    }

    public function testParamsFollowTheFirstColonAndSplitOnCommas(): void
    {
        $errors = (new Validator())->validate(['time' => '09:00'], ['time' => 'in:10:00,12:30']);

        $this->assertSame(['time' => [['rule' => 'in', 'params' => ['10:00', '12:30']]]], $errors);
    }

    public function testNullableInsideAParameterListIsNotTheFlag(): void
    {
        $errors = (new Validator())->validate(['status' => ''], ['status' => 'in:nullable,active']);

        $this->assertSame(['status' => [['rule' => 'in', 'params' => ['nullable', 'active']]]], $errors);
    }

    public function testUnknownRuleThrows(): void
    {
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage("Unknown validation rule 'requird' in 'requird|email'");

        (new Validator())->validate(['email' => 'user@example.com'], ['email' => 'requird|email']);
    }

    #[DataProvider('invalidParams')]
    public function testInvalidParamsThrow(string $rules, string $message): void
    {
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage($message);

        (new Validator())->validate(['field' => 'value'], ['field' => $rules]);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalidParams(): iterable
    {
        yield 'min: not a number' => ['min:abc', "Invalid rule 'min:abc': expects one non-negative integer."];
        yield 'max: missing' => ['max', "Invalid rule 'max': expects one non-negative integer."];
        yield 'length: negative' => ['length:-1', "Invalid rule 'length:-1': expects one non-negative integer."];
        yield 'min_value: not a number' => ['min_value:x', "Invalid rule 'min_value:x': expects one number."];
        yield 'min_value: infinite' => ['min_value:1e400', "Invalid rule 'min_value:1e400': expects one number."];
        yield 'max_value: two numbers' => ['max_value:1,2', "Invalid rule 'max_value:1,2': expects one number."];
        yield 'in: no values' => ['in:', "Invalid rule 'in:': expects at least one value."];
        yield 'regex: invalid pattern' => ['regex:invalid', "Invalid rule 'regex:invalid': expects a valid PCRE pattern."];
        yield 'phone: unknown region' => ['phone:XX', "Invalid rule 'phone:XX': expects an optional region code such as US."];
        yield 'required_if: no value' => [
            'required_if:type',
            "Invalid rule 'required_if:type': expects a field and at least one value.",
        ];
        yield 'required_without: no field' => ['required_without', "Invalid rule 'required_without': expects at least one field."];
        yield 'prohibited_if: no value' => [
            'prohibited_if:type',
            "Invalid rule 'prohibited_if:type': expects a field and at least one value.",
        ];
        yield 'prohibited_unless: no field' => [
            'prohibited_unless',
            "Invalid rule 'prohibited_unless': expects a field and at least one value.",
        ];
    }

    public function testParamErrorNamesTheRuleAsWrittenAndItsRuleString(): void
    {
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage("Invalid rule 'size:abc' in 'required|size:abc': expects one non-negative integer.");

        (new Validator(['size' => new LengthRule()]))->validate(['code' => 'abc'], ['code' => 'required|size:abc']);
    }

    public function testInvalidParamsThrowEvenWhenTheRuleIsNotReached(): void
    {
        $this->expectException(InvalidRuleException::class);

        // `required` fails and ends the field before min:abc would run.
        (new Validator())->validate(['name' => ''], ['name' => 'required|min:abc']);
    }

    public function testAbsentFieldIsCheckedByPresenceRulesOnly(): void
    {
        $validator = new Validator();

        $this->assertSame([], $validator->validate([], ['email' => 'email|min:5']));
        $this->assertSame(
            ['email' => [['rule' => 'required']]],
            $validator->validate([], ['email' => 'required|email|min:5']),
        );
    }

    #[DataProvider('blankValues')]
    public function testNullableBlankValueIsCheckedByPresenceRulesOnly(mixed $blank): void
    {
        $validator = new Validator();

        $this->assertSame([], $validator->validate(['site' => $blank], ['site' => 'nullable|regex:/^https?:/']));
        $this->assertSame(
            ['site' => [['rule' => 'required']]],
            $validator->validate(['site' => $blank], ['site' => 'nullable|required|regex:/^https?:/']),
        );
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function blankValues(): iterable
    {
        yield 'null' => [null];
        yield 'empty string' => [''];
        yield 'empty array' => [[]];
    }

    public function testWhitespaceIsNotBlankForNullable(): void
    {
        $validator = new Validator();

        $this->assertSame(
            ['email' => [['rule' => 'email']]],
            $validator->validate(['email' => '   '], ['email' => 'nullable|email']),
        );
        $this->assertSame(
            ['name' => [['rule' => 'required']]],
            $validator->validate(['name' => '   '], ['name' => 'nullable|required']),
        );
    }

    public function testNullableIsRecognizedWithWhitespace(): void
    {
        $errors = (new Validator())->validate(['code' => ''], ['code' => 'min:3 | nullable']);

        $this->assertSame([], $errors);
    }

    public function testNullableDoesNotSkipFalsyValues(): void
    {
        $errors = (new Validator())->validate(['code' => '0'], ['code' => 'nullable|min:5']);

        $this->assertSame(['code' => [['rule' => 'min', 'params' => ['5']]]], $errors);
    }

    public function testNullIsNoValueLikeAnAbsentField(): void
    {
        $validator = new Validator();
        $rules = [
            'email' => 'email|max:5',
            'name' => 'required|string',
            'nickname' => 'filled|string',
            'note' => 'present|string',
            'company' => 'string|required_if:type,company',
        ];

        $this->assertSame([], $validator->validate(['email' => null, 'name' => 'Ann', 'note' => null], $rules));
        $this->assertSame(
            [
                'name' => [['rule' => 'required']],
                'nickname' => [['rule' => 'filled']],
                'note' => [['rule' => 'present']],
                'company' => [['rule' => 'required_if', 'params' => ['type', 'company']]],
            ],
            $validator->validate(['name' => null, 'nickname' => null, 'type' => 'company', 'company' => null], $rules),
        );
    }

    public function testBlankValueWithoutNullableIsValidated(): void
    {
        $validator = new Validator();

        $this->assertSame(['email' => [['rule' => 'email']]], $validator->validate(['email' => ''], ['email' => 'email']));
        $this->assertSame(
            ['tags' => [['rule' => 'min', 'params' => ['1']]]],
            $validator->validate(['tags' => []], ['tags' => 'array|min:1']),
        );
    }

    public function testFirstFailingRuleEndsTheField(): void
    {
        $validator = new Validator();

        $this->assertSame(
            ['age' => [['rule' => 'integer']]],
            $validator->validate(['age' => 'abc'], ['age' => 'integer|min_value:18']),
        );
        $this->assertSame(
            ['email' => [['rule' => 'required']]],
            $validator->validate(['email' => ''], ['email' => 'required|email|min:5']),
        );
    }

    public function testClosureRules(): void
    {
        $validator = new Validator([
            'even' => fn (mixed $value): bool => is_int($value) && $value % 2 === 0,
            'divisible_by' => fn (mixed $value, array $params): bool => is_int($value) && $value % (int)$params[0] === 0,
        ]);

        $this->assertSame([], $validator->validate(['n' => 4], ['n' => 'even']));
        $this->assertSame(['n' => [['rule' => 'even']]], $validator->validate(['n' => 3], ['n' => 'even']));
        $this->assertSame(
            ['n' => [['rule' => 'divisible_by', 'params' => ['5']]]],
            $validator->validate(['n' => 7], ['n' => 'divisible_by:5']),
        );
    }

    public function testAnyCallableIsARule(): void
    {
        $odd = new class {
            public function __invoke(mixed $value): bool
            {
                return is_int($value) && $value % 2 === 1;
            }
        };
        $validator = new Validator(['even' => [self::class, 'isEven'], 'odd' => $odd]);

        $this->assertSame([], $validator->validate(['a' => 2, 'b' => 3], ['a' => 'even', 'b' => 'odd']));
        $this->assertSame(
            ['a' => [['rule' => 'even']], 'b' => [['rule' => 'odd']]],
            $validator->validate(['a' => 3, 'b' => 2], ['a' => 'even', 'b' => 'odd']),
        );
    }

    public static function isEven(mixed $value): bool
    {
        return is_int($value) && $value % 2 === 0;
    }

    public function testRuleThatIsNotCallableFailsOnConstruction(): void
    {
        $this->expectException(\TypeError::class);

        new Validator(['even' => 'no_such_function']);
    }

    #[DataProvider('unreachableNames')]
    public function testRuleNameNoRuleStringCanReferToIsRejected(string $name): void
    {
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage("Cannot register rule '{$name}'");

        new Validator([$name => fn (): bool => true]);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function unreachableNames(): iterable
    {
        yield 'reserved nullable' => ['nullable'];
        yield 'empty' => [''];
        yield 'colon' => ['date:iso'];
        yield 'pipe' => ['a|b'];
        yield 'surrounding whitespace' => [' padded'];
    }

    public function testClassRuleReturnsItsOwnError(): void
    {
        $strength = new class implements RuleInterface {
            public function validate(mixed $value, array $params, Field $field): ?array
            {
                return is_string($value) && strlen($value) >= 8
                    ? null
                    : ['rule' => 'password_strength', 'params' => ['too_short']];
            }
        };

        $errors = (new Validator(['strong' => $strength]))->validate(['password' => 'abc'], ['password' => 'strong']);

        $this->assertSame(['password' => [['rule' => 'password_strength', 'params' => ['too_short']]]], $errors);
    }

    public function testCustomRuleOverridesBuiltInRule(): void
    {
        $validator = new Validator(['email' => fn (mixed $value): bool => $value === 'admin']);

        $this->assertSame([], $validator->validate(['login' => 'admin'], ['login' => 'email']));
    }

    public function testRuleSeesItsFieldAndThePayload(): void
    {
        $validator = new Validator([
            'confirmed' => fn (mixed $value, array $params, Field $field): bool
                => $value === ($field->data[$field->name . '_confirmation'] ?? null),
        ]);

        $this->assertSame([], $validator->validate(
            ['password' => 'secret', 'password_confirmation' => 'secret'],
            ['password' => 'confirmed'],
        ));
        $this->assertSame(['password' => [['rule' => 'confirmed']]], $validator->validate(
            ['password' => 'secret', 'password_confirmation' => 'other'],
            ['password' => 'confirmed'],
        ));
    }

    public function testCustomPresenceRuleRunsOnNullableBlankValue(): void
    {
        $requiredForAdmin = new class implements RuleInterface, PresenceRule {
            public function validate(mixed $value, array $params, Field $field): ?array
            {
                $required = ($field->data['role'] ?? null) === 'admin';
                return $required && ($value === null || $value === '') ? ['rule' => 'required_for_admin'] : null;
            }
        };
        $validator = new Validator(['required_for_admin' => $requiredForAdmin]);
        $rules = ['token' => 'nullable|required_for_admin'];

        $this->assertSame(
            ['token' => [['rule' => 'required_for_admin']]],
            $validator->validate(['role' => 'admin', 'token' => ''], $rules),
        );
        $this->assertSame([], $validator->validate(['role' => 'user', 'token' => ''], $rules));
    }

    public function testCustomParameterizedRuleIsCheckedOnCompile(): void
    {
        $unique = new class implements RuleInterface, ParameterizedRule {
            public function checkParams(array $params): void
            {
                if (count($params) !== 2) {
                    throw InvalidRuleException::invalidParams('a table and a column');
                }
            }

            public function validate(mixed $value, array $params, Field $field): ?array
            {
                return null;
            }
        };

        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage("Invalid rule 'unique:users': expects a table and a column.");

        (new Validator(['unique' => $unique]))->validate([], ['email' => 'unique:users']);
    }

    public function testRepeatedCallsAreIndependent(): void
    {
        $validator = new Validator();
        $rules = ['qty' => 'required|integer|min_value:1'];

        $this->assertSame(['qty' => [['rule' => 'min_value', 'params' => ['1']]]], $validator->validate(['qty' => '0'], $rules));
        $this->assertSame([], $validator->validate(['qty' => '2'], $rules));
        $this->assertSame(['qty' => [['rule' => 'required']]], $validator->validate([], $rules));
    }

    public function testCompiledRuleStringsAreBounded(): void
    {
        $validator = new Validator();
        for ($i = 0; $i <= 1000; $i++) {
            $validator->validate(['name' => 'John'], ['name' => "required|max:{$i}"]);
        }

        $compiled = (new \ReflectionProperty(Validator::class, 'compiled'))->getValue($validator);
        $this->assertCount(1000, $compiled);
        $this->assertArrayNotHasKey('required|max:0', $compiled);
        $this->assertSame(
            ['name' => [['rule' => 'max', 'params' => ['0']]]],
            $validator->validate(['name' => 'John'], ['name' => 'required|max:0']),
        );
    }

    public function testRuleMayValidateANestedPayloadWithTheSameInstance(): void
    {
        $validator = new Validator([
            'address' => function (mixed $value) use (&$validator): bool {
                return $validator->validate(is_array($value) ? $value : [], ['street' => 'required']) === [];
            },
        ]);

        $errors = $validator->validate(
            ['name' => '', 'address' => ['street' => '']],
            ['name' => 'required', 'address' => 'address'],
        );

        $this->assertSame([
            'name' => [['rule' => 'required']],
            'address' => [['rule' => 'address']],
        ], $errors);
    }

    public function testIntegerLikeFieldNames(): void
    {
        // PHP stores the key '0' as the int 0.
        $errors = (new Validator())->validate(['0' => ''], ['0' => 'required']);

        $this->assertSame([0 => [['rule' => 'required']]], $errors);
    }
}
