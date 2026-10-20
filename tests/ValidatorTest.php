<?php

declare(strict_types=1);

namespace Solo\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Solo\Validator\PresenceRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Validator;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testRequiredValidation(): void
    {
        $data = ['name' => ''];
        $rules = ['name' => 'required'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('name', $errors);
    }

    public function testRequiredValidationWithNull(): void
    {
        $data = ['name' => null];
        $rules = ['name' => 'required'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('name', $errors);
    }

    public function testRequiredValidationPasses(): void
    {
        $data = ['name' => 'John'];
        $rules = ['name' => 'required'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testEmailValidation(): void
    {
        $data = ['email' => 'invalid-email'];
        $rules = ['email' => 'email'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('email', $errors);
    }

    public function testEmailValidationPasses(): void
    {
        $data = ['email' => 'user@example.com'];
        $rules = ['email' => 'email'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testMinValidation(): void
    {
        $data = ['password' => '123'];
        $rules = ['password' => 'min:8'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('password', $errors);
    }

    public function testMinValidationPasses(): void
    {
        $data = ['password' => '12345678'];
        $rules = ['password' => 'min:8'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testMaxValidation(): void
    {
        $data = ['username' => 'verylongusername'];
        $rules = ['username' => 'max:10'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('username', $errors);
    }

    public function testMaxValidationPasses(): void
    {
        $data = ['username' => 'john'];
        $rules = ['username' => 'max:10'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testLengthValidation(): void
    {
        $data = ['pin' => '123'];
        $rules = ['pin' => 'length:4'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('pin', $errors);
    }

    public function testLengthValidationPasses(): void
    {
        $data = ['pin' => '1234'];
        $rules = ['pin' => 'length:4'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testIntegerValidation(): void
    {
        $data = ['age' => 'not-a-number'];
        $rules = ['age' => 'integer'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('age', $errors);
    }

    public function testIntegerValidationPasses(): void
    {
        $data = ['age' => 25];
        $rules = ['age' => 'integer'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testStringValidation(): void
    {
        $data = ['name' => 123];
        $rules = ['name' => 'string'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('name', $errors);
    }

    public function testStringValidationPasses(): void
    {
        $data = ['name' => 'John'];
        $rules = ['name' => 'string'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testNumericValidation(): void
    {
        $data = ['price' => 'not-numeric'];
        $rules = ['price' => 'numeric'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('price', $errors);
    }

    public function testNumericValidationPasses(): void
    {
        $data = ['price' => 99.99];
        $rules = ['price' => 'numeric'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testArrayValidation(): void
    {
        $data = ['tags' => 'not-an-array'];
        $rules = ['tags' => 'array'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('tags', $errors);
    }

    public function testArrayValidationPasses(): void
    {
        $data = ['tags' => ['php', 'validator']];
        $rules = ['tags' => 'array'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testMinValueValidation(): void
    {
        $data = ['age' => 15];
        $rules = ['age' => 'min_value:18'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('age', $errors);
    }

    public function testMinValueValidationPasses(): void
    {
        $data = ['age' => 25];
        $rules = ['age' => 'min_value:18'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testMaxValueValidation(): void
    {
        $data = ['price' => 150];
        $rules = ['price' => 'max_value:100'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('price', $errors);
    }

    public function testMaxValueValidationPasses(): void
    {
        $data = ['price' => 99];
        $rules = ['price' => 'max_value:100'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testFilledValidation(): void
    {
        $data = ['description' => ''];
        $rules = ['description' => 'filled'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('description', $errors);
    }

    public function testFilledValidationPasses(): void
    {
        $data = ['description' => 'Some description'];
        $rules = ['description' => 'filled'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testFilledValidationWithNull(): void
    {
        $data = ['description' => null];
        $rules = ['description' => 'filled'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('description', $errors);
    }

    public function testRegexValidation(): void
    {
        $data = ['username' => 'user@name'];
        $rules = ['username' => 'regex:/^[a-zA-Z0-9_]+$/'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('username', $errors);
    }

    public function testRegexValidationPasses(): void
    {
        $data = ['username' => 'username123'];
        $rules = ['username' => 'regex:/^[a-zA-Z0-9_]+$/'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testRegexValidationWithInvalidPattern(): void
    {
        $data = ['username' => 'test'];
        $rules = ['username' => 'regex:invalid'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('username', $errors);
    }

    public function testNullableValidation(): void
    {
        $data = [];
        $rules = ['optional_field' => 'nullable|email'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testNullableSkipsRulesForEmptyString(): void
    {
        $data = ['website' => ''];
        $rules = ['website' => 'nullable|regex:/^https?:\/\//'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testNullableSkipsRulesForEmptyArray(): void
    {
        $data = ['tags' => []];
        $rules = ['tags' => 'nullable|in:php,js'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testNullableSkipsCustomCallableRule(): void
    {
        $this->validator->addCustomRule('even', fn ($value) => (int)$value % 2 === 0);

        $data = ['number' => ''];
        $rules = ['number' => 'nullable|even'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testNullableDoesNotSkipNonBlankFalsyValue(): void
    {
        $data = ['code' => '0'];
        $rules = ['code' => 'nullable|min:5'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertSame([['rule' => 'min', 'params' => ['5']]], $errors['code']);
    }

    public function testNullableDoesNotSkipRequired(): void
    {
        $data = ['name' => null];
        $rules = ['name' => 'required|nullable'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertSame([['rule' => 'required']], $errors['name']);
    }

    public function testNullableDoesNotSkipFilled(): void
    {
        $data = ['description' => ''];
        $rules = ['description' => 'filled|nullable'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertSame([['rule' => 'filled']], $errors['description']);
    }

    /**
     * The reason PresenceRule exists: a custom conditional rule (here a minimal
     * `required_if`) must still run on a blank value of a nullable field, where
     * `nullable|required_if` is a meaningful, non-contradictory combination.
     */
    public function testNullableRunsCustomPresenceRule(): void
    {
        $requiredIf = new class implements RuleInterface, PresenceRule {
            public function validate(mixed $value, ?string $parameter, array $data = []): ?array
            {
                [$field, $expected] = explode(',', (string)$parameter);
                $isRequired = (string)($data[$field] ?? '') === $expected;
                if ($isRequired && ($value === null || $value === '')) {
                    return ['rule' => 'required_if', 'params' => [$field, $expected]];
                }
                return null;
            }
        };
        $this->validator->addRule('required_if', $requiredIf);

        // x=1 → password is required even though it is blank and nullable.
        $errors = $this->validator->validate(
            ['x' => '1', 'password' => ''],
            ['password' => 'nullable|required_if:x,1']
        );
        $this->assertTrue($this->validator->fails());
        $this->assertSame([['rule' => 'required_if', 'params' => ['x', '1']]], $errors['password']);

        // x=0 → not required → blank value is skipped by nullable and passes.
        $errors = $this->validator->validate(
            ['x' => '0', 'password' => ''],
            ['password' => 'nullable|required_if:x,1']
        );
        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testMultipleRules(): void
    {
        $data = [
            'email' => 'invalid-email',
            'password' => '123',
            'age' => 'not-a-number'
        ];
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8',
            'age' => 'required|integer|min_value:18'
        ];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertArrayHasKey('age', $errors);
    }

    public function testCustomRule(): void
    {
        $this->validator->addCustomRule('even', function ($value) {
            return (int)$value % 2 === 0;
        });

        $data = ['number' => 3];
        $rules = ['number' => 'even'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('number', $errors);
    }

    public function testCustomRulePasses(): void
    {
        $this->validator->addCustomRule('even', function ($value) {
            return (int)$value % 2 === 0;
        });

        $data = ['number' => 4];
        $rules = ['number' => 'even'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testErrorsReturnStructuredFormat(): void
    {
        $data = [
            'email' => 'invalid-email',
            'password' => '123'
        ];
        $rules = [
            'email' => 'email',
            'password' => 'min:8'
        ];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertSame([['rule' => 'email']], $errors['email']);
        $this->assertSame([['rule' => 'min', 'params' => ['8']]], $errors['password']);
    }

    public function testInValidation(): void
    {
        $data = ['status' => 'pending'];
        $rules = ['status' => 'in:active,inactive,draft'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('status', $errors);
    }

    public function testInValidationPasses(): void
    {
        $data = ['status' => 'active'];
        $rules = ['status' => 'in:active,inactive,draft'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testInValidationWithoutParameter(): void
    {
        $data = ['status' => 'active'];
        $rules = ['status' => 'in'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('status', $errors);
    }

    public function testInValidationWithArrayPasses(): void
    {
        $data = ['tags' => ['php', 'javascript']];
        $rules = ['tags' => 'in:php,javascript,python,go'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testInValidationWithArrayFails(): void
    {
        $data = ['tags' => ['php', 'ruby']];
        $rules = ['tags' => 'in:php,javascript,python,go'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('tags', $errors);
    }

    public function testBooleanValidation(): void
    {
        $data = ['is_active' => 'invalid'];
        $rules = ['is_active' => 'boolean'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('is_active', $errors);
    }

    public function testBooleanValidationWithBool(): void
    {
        $data = ['is_active' => true];
        $rules = ['is_active' => 'boolean'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testBooleanValidationWithString(): void
    {
        $data = ['is_active' => 'yes'];
        $rules = ['is_active' => 'boolean'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testBooleanValidationWithInt(): void
    {
        $data = ['is_active' => 1];
        $rules = ['is_active' => 'boolean'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testErrorsReturnStructuredFormatWithParams(): void
    {
        $data = ['status' => 'pending'];
        $rules = ['status' => 'in:active,inactive'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertSame([['rule' => 'in', 'params' => ['active', 'inactive']]], $errors['status']);
    }

    public function testErrorsReturnStructuredFormatWithoutParams(): void
    {
        $data = ['name' => ''];
        $rules = ['name' => 'required'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertSame([['rule' => 'required']], $errors['name']);
    }

    public function testMultipleErrorsPerField(): void
    {
        $data = ['age' => 'abc'];
        $rules = ['age' => 'integer|min_value:18'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertCount(2, $errors['age']);
        $this->assertSame('integer', $errors['age'][0]['rule']);
        $this->assertSame('numeric', $errors['age'][1]['rule']);
    }

    public function testCustomRuleErrorStructuredFormat(): void
    {
        $this->validator->addCustomRule('even', function ($value) {
            return (int)$value % 2 === 0;
        });

        $data = ['number' => 3];
        $rules = ['number' => 'even'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertSame([['rule' => 'even']], $errors['number']);
    }

    public function testCustomRuleWithParamErrorStructuredFormat(): void
    {
        $this->validator->addCustomRule('divisible', function ($value, $param) {
            return (int)$value % (int)$param === 0;
        });

        $data = ['number' => 7];
        $rules = ['number' => 'divisible:3'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertSame([['rule' => 'divisible', 'params' => ['3']]], $errors['number']);
    }

    public function testDateValidation(): void
    {
        $data = ['birth_date' => 'not-a-date'];
        $rules = ['birth_date' => 'date'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('birth_date', $errors);
    }

    public function testDateValidationPasses(): void
    {
        $data = ['birth_date' => '2024-01-15'];
        $rules = ['birth_date' => 'date'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testDateValidationWithNumericTimestamp(): void
    {
        $data = ['timestamp' => 1705334400];
        $rules = ['timestamp' => 'date'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testDateValidationWithNonStringNonNumeric(): void
    {
        $data = ['birth_date' => ['2024-01-15']];
        $rules = ['birth_date' => 'date'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('birth_date', $errors);
    }

    public function testDateWithFormatValidation(): void
    {
        $data = ['event_date' => '15-01-2024'];
        $rules = ['event_date' => 'date:Y-m-d'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('event_date', $errors);
    }

    public function testDateWithFormatValidationPasses(): void
    {
        $data = ['event_date' => '2024-01-15'];
        $rules = ['event_date' => 'date:Y-m-d'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testArrayWithAllowedKeys(): void
    {
        $data = ['user' => ['name' => 'John', 'email' => 'john@example.com']];
        $rules = ['user' => 'array:name,email'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testArrayWithInvalidKeys(): void
    {
        $data = ['user' => ['name' => 'John', 'password' => 'secret']];
        $rules = ['user' => 'array:name,email'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('user', $errors);
    }

    public function testPhoneValidation(): void
    {
        $data = ['phone' => 'not-a-phone'];
        $rules = ['phone' => 'phone:US'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('phone', $errors);
    }

    public function testPhoneValidationPasses(): void
    {
        $data = ['phone' => '+14155552671'];
        $rules = ['phone' => 'phone:US'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testPhoneValidationInvalidNumber(): void
    {
        $data = ['phone' => '+14155'];
        $rules = ['phone' => 'phone:US'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('phone', $errors);
    }

    public function testPhoneWithoutRegionRejectsNationalNumber(): void
    {
        $data = ['phone' => '4155552671'];
        $rules = ['phone' => 'phone'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertSame([['rule' => 'phone']], $errors['phone']);
    }

    public function testPhoneWithoutRegionAcceptsInternationalNumber(): void
    {
        $data = ['phone' => '+14155552671'];
        $rules = ['phone' => 'phone'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testPhoneWithUnknownRegionRejectsNationalNumber(): void
    {
        $data = ['phone' => '4155552671'];
        $rules = ['phone' => 'phone:XX'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertSame([['rule' => 'phone']], $errors['phone']);
    }

    public function testPhoneWithUnknownCountryCode(): void
    {
        $data = ['phone' => '+999 1234567'];
        $rules = ['phone' => 'phone:US'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertSame([['rule' => 'phone']], $errors['phone']);
    }

    public function testMaxValueValidationWithNonNumeric(): void
    {
        $data = ['price' => 'not-a-number'];
        $rules = ['price' => 'max_value:100'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertArrayHasKey('price', $errors);
    }

    public function testErrorsMethod(): void
    {
        $data = ['email' => 'invalid'];
        $rules = ['email' => 'email'];

        $this->validator->validate($data, $rules);

        $errors = $this->validator->errors();
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('email', $errors);
    }

    public function testUnknownRuleIsIgnored(): void
    {
        $data = ['field' => 'value'];
        $rules = ['field' => 'unknown_rule'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testUuidValidation(): void
    {
        $data = ['id' => 'not-a-uuid'];
        $rules = ['id' => 'uuid'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertSame([['rule' => 'uuid']], $errors['id']);
    }

    public function testUuidValidationPasses(): void
    {
        $data = ['id' => '550e8400-e29b-41d4-a716-446655440000'];
        $rules = ['id' => 'uuid'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testUuidValidationWithUppercase(): void
    {
        $data = ['id' => '550E8400-E29B-41D4-A716-446655440000'];
        $rules = ['id' => 'uuid'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testUuidValidationWithNonString(): void
    {
        $data = ['id' => 12345];
        $rules = ['id' => 'uuid'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertSame([['rule' => 'uuid']], $errors['id']);
    }

    public function testWhitespaceAroundRulesIsIgnored(): void
    {
        $data = ['email' => 'not-an-email'];
        $rules = ['email' => ' required | email '];

        $errors = $this->validator->validate($data, $rules);

        $this->assertSame([['rule' => 'email']], $errors['email']);
    }

    public function testNullableIsRecognizedWithWhitespace(): void
    {
        $data = ['code' => ''];
        $rules = ['code' => 'min:3 | nullable'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    public function testEmptyRuleSegmentsAreSkipped(): void
    {
        $data = ['name' => ''];
        $rules = ['name' => '|required||'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertSame([['rule' => 'required']], $errors['name']);
    }

    public function testStringRulesRejectArraysWithoutWarnings(): void
    {
        $data = ['pin' => ['1234'], 'slug' => ['a'], 'tags' => [['php']]];
        $rules = ['pin' => 'length:4', 'slug' => 'regex:/a/', 'tags' => 'in:php,js'];

        $errors = $this->validateWithStrictErrors($data, $rules);

        $this->assertSame([
            'pin' => [['rule' => 'length', 'params' => ['4']]],
            'slug' => [['rule' => 'regex']],
            'tags' => [['rule' => 'in', 'params' => ['php', 'js']]],
        ], $errors);
    }

    public function testStringRulesRejectObjectsWithoutToString(): void
    {
        $object = new \stdClass();
        $data = ['pin' => $object, 'name' => $object, 'title' => $object, 'slug' => $object, 'status' => $object];
        $rules = [
            'pin' => 'length:3',
            'name' => 'min:1',
            'title' => 'max:3',
            'slug' => 'regex:/a/',
            'status' => 'in:a,b',
        ];

        $errors = $this->validateWithStrictErrors($data, $rules);

        $this->assertSame([
            'pin' => [['rule' => 'length', 'params' => ['3']]],
            'name' => [['rule' => 'min', 'params' => ['1']]],
            'title' => [['rule' => 'max', 'params' => ['3']]],
            'slug' => [['rule' => 'regex']],
            'status' => [['rule' => 'in', 'params' => ['a', 'b']]],
        ], $errors);
    }

    public function testStringRulesReadStringableObjects(): void
    {
        $stringable = new class {
            public function __toString(): string
            {
                return 'abc';
            }
        };
        $data = ['code' => $stringable];
        $rules = ['code' => 'length:3|min:3|max:3|regex:/^abc$/|in:abc,def'];

        $errors = $this->validateWithStrictErrors($data, $rules);

        $this->assertTrue($this->validator->passed());
        $this->assertEmpty($errors);
    }

    /**
     * A rule may validate a nested payload with the same validator instance
     * (e.g. one injected as ValidatorInterface): the outer call keeps its own
     * errors and does not pick up the nested ones.
     */
    public function testNestedValidateCallKeepsOuterErrors(): void
    {
        $address = new class ($this->validator) implements RuleInterface {
            public function __construct(private Validator $validator)
            {
            }

            public function validate(mixed $value, ?string $parameter, array $data = []): ?array
            {
                $errors = $this->validator->validate(is_array($value) ? $value : [], ['street' => 'required']);
                return $errors === [] ? null : ['rule' => 'address'];
            }
        };
        $this->validator->addRule('address', $address);

        $errors = $this->validator->validate(
            ['name' => '', 'address' => ['street' => '']],
            ['name' => 'required', 'address' => 'address']
        );

        $expected = [
            'name' => [['rule' => 'required']],
            'address' => [['rule' => 'address']],
        ];
        $this->assertSame($expected, $errors);
        $this->assertSame($expected, $this->validator->errors());
        $this->assertTrue($this->validator->fails());
    }

    /**
     * Validate the way an application with a framework error handler does
     * (Symfony, Laravel): a PHP warning becomes an exception, i.e. a 500.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @return array<string, list<array{rule: string, params?: string[]}>>
     */
    private function validateWithStrictErrors(array $data, array $rules): array
    {
        set_error_handler(static function (int $errno, string $errstr): bool {
            if (!(error_reporting() & $errno)) {
                return false; // silenced with @
            }
            throw new \ErrorException($errstr, 0, $errno);
        });

        try {
            return $this->validator->validate($data, $rules);
        } finally {
            restore_error_handler();
        }
    }
}
