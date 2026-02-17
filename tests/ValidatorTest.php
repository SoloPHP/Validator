<?php

declare(strict_types=1);

namespace Solo\Validator\Tests;

use PHPUnit\Framework\TestCase;
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
}
