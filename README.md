# PHP Validator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/solophp/validator.svg)](https://packagist.org/packages/solophp/validator)
[![License](https://img.shields.io/packagist/l/solophp/validator.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/packagist/php-v/solophp/validator.svg)](https://php.net)
[![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)]()

**Solo Validator** is a lightweight, standalone PHP validation library designed for simplicity and flexibility. It provides essential validation rules out of the box, supports custom rules and messages, and integrates seamlessly into any PHP project.

## Features

- **Basic Validation Rules**: Includes `required`, `email`, `phone`, `min`, `max`, `length`, and more
- **Custom Rules**: Extend validation with your own rules
- **Custom Error Messages**: Override default messages globally or per validation
- **Parameterized Rules**: Define rules with parameters (e.g., `min:8`)
- **Placeholder Support**: Dynamic messages with `:field` and `:param` placeholders
- **Standardized Contracts**: Uses `solophp/contracts` for consistent validation interface
- **PSR-4 Compliant**: Modern autoloading structure
- **Comprehensive Testing**: Full test coverage with PHPUnit
- **Code Standards**: PSR-12 compliant code

## Installation

Install via Composer:

```bash
composer require solophp/validator
```

## Quick Start

```php
<?php
use Solo\Validator\Validator;

$validator = new Validator();

$data = [
    'email' => 'user@example.com',
    'username' => 'john_doe',
    'pin_code' => '1234',
    'age' => 25,
];

$rules = [
    'email' => 'required|email',
    'username' => 'required|min:3|max:20',
    'pin_code' => 'required|length:4',
    'age' => 'integer|min_value:18',
];

$errors = $validator->validate($data, $rules);

if ($validator->fails()) {
    print_r($validator->errors());
} else {
    echo "Validation passed!";
}
```

## Available Validation Rules

### Core Rules

- **required**: The field must not be empty
- **email**: The field must be a valid email address
- **phone**: The field must be a valid phone number
- **length:value**: The field must be exactly `value` characters long
- **min:value**: The field must have a minimum length of `value`
- **max:value**: The field must not exceed `value` in length
- **filled**: The field must not be empty
- **integer**: The field must be an integer
- **string**: The field must be a string
- **regex**: The field must match the provided regex pattern
- **numeric**: The field must be a number
- **array**: The field must be an array
- **array:key1,key2,...**: The field must be an array containing only the specified keys
- **boolean**: The field must be true or false (accepts: true/false, 1/0, '1'/'0', 'true'/'false', 'yes'/'no', 'on'/'off'; case-insensitive)
- **min_value:value**: The field must be at least `value`
- **max_value:value**: The field must not exceed `value`
- **in:value1,value2,...**: The field must be one of the specified values (supports arrays)
- **nullable**: The field can be null or empty
- **date**: The field must be a valid date
- **date:format**: The field must match the specified date format (e.g., `date:Y-m-d`)

### Example Usage

```php
$rules = [
    'username' => 'required|min:3|max:30',
    'pin_code' => 'required|length:4',
    'age' => 'required|integer|min_value:18',
    'email' => 'required|email',
    'phone' => 'phone:US',
    'tags' => 'array',
    'metadata' => 'array:name,email,phone',
    'price' => 'numeric|min_value:0|max_value:1000',
    'status' => 'required|in:active,inactive,draft',
    'is_active' => 'boolean',
    'birth_date' => 'date',
    'event_date' => 'date:Y-m-d'
];
```

## Custom Validation Rules

Add your own validation logic using `addCustomRule()`:

```php
$validator->addCustomRule('even', function ($value, $param, $data) {
    return (int)$value % 2 === 0;
});

// Usage
$rules = ['number' => 'even'];
$messages = ['number.even' => 'The number must be even.'];
```

## Custom Error Messages

Override default messages globally or during validation:

```php
// Global messages
$messages = [
    'required' => 'Custom required message.',
    'email.email' => 'Invalid email format.'
];
$validator = new Validator($messages);

// Per-validation messages
$errors = $validator->validate($data, $rules, [
    'password.min' => 'Password must be at least 8 characters.'
]);
```

## Error Handling

- **`fails()`**: Check if validation failed
- **`errors()`**: Get all validation errors
- **`passed()`**: Check if validation succeeded

```php
if ($validator->fails()) {
    foreach ($validator->errors() as $field => $messages) {
        echo "$field: " . implode(', ', $messages);
    }
}
```

## Placeholders in Messages

Use `:field` and `:param` in messages for dynamic content:

```php
// Default message for 'min' rule:
'The :field must be at least :param characters.'

// Becomes:
'The password must be at least 8 characters.'
```

## Development

### Running Tests

```bash
composer test
```

### Code Standards

Check code standards:
```bash
composer cs
```

Fix code standards:
```bash
composer cs-fix
```

## Contributing

Contributions are welcome! Please open an issue or submit a pull request.

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.