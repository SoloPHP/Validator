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
}
