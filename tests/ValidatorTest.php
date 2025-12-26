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

    public function testCustomMessages(): void
    {
        $messages = [
            'email.email' => 'Please provide a valid email address.',
            'password.min' => 'Password must be at least 8 characters long.'
        ];

        $data = [
            'email' => 'invalid-email',
            'password' => '123'
        ];
        $rules = [
            'email' => 'email',
            'password' => 'min:8'
        ];

        $errors = $this->validator->validate($data, $rules, $messages);

        $this->assertTrue($this->validator->fails());
        $this->assertContains('Please provide a valid email address.', $errors['email']);
        $this->assertContains('Password must be at least 8 characters long.', $errors['password']);
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

    public function testGlobalCustomMessages(): void
    {
        $messages = [
            'required' => 'This field is required.',
            'email' => 'Please provide a valid email address.'
        ];

        $validator = new Validator($messages);

        $data = ['email' => 'invalid-email'];
        $rules = ['email' => 'required|email'];

        $errors = $validator->validate($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertContains('Please provide a valid email address.', $errors['email']);
    }

    public function testPlaceholderSubstitutionInDefaultMessages(): void
    {
        $data = ['username' => 'ab'];
        $rules = ['username' => 'min:3'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertTrue($this->validator->fails());
        $this->assertContains('The username must be at least 3.', $errors['username']);
    }

    public function testPlaceholderSubstitutionInCustomMessages(): void
    {
        $data = ['password' => '12345'];
        $rules = ['password' => 'min:8'];
        $messages = ['min' => 'The :field field must have at least :param characters.'];

        $errors = $this->validator->validate($data, $rules, $messages);

        $this->assertTrue($this->validator->fails());
        $this->assertContains('The password field must have at least 8 characters.', $errors['password']);
    }

    public function testPlaceholderSubstitutionInFieldSpecificMessages(): void
    {
        $data = ['email' => 'test'];
        $rules = ['email' => 'min:5'];
        $messages = ['email.min' => 'Email must be at least :param characters long.'];

        $errors = $this->validator->validate($data, $rules, $messages);

        $this->assertTrue($this->validator->fails());
        $this->assertContains('Email must be at least 5 characters long.', $errors['email']);
    }

    public function testPlaceholderSubstitutionInGlobalMessages(): void
    {
        $messages = [
            'max' => 'The :field cannot exceed :param characters.'
        ];
        $validator = new Validator($messages);

        $data = ['title' => 'This is a very long title that exceeds limit'];
        $rules = ['title' => 'max:20'];

        $errors = $validator->validate($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertContains('The title cannot exceed 20 characters.', $errors['title']);
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
}
