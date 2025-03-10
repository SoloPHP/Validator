# PHP Validator

[![Version](https://img.shields.io/badge/version-2.1.0-blue.svg)](https://github.com/solophp/validator)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)

**Solo Validator** is a lightweight, standalone PHP validation library designed for simplicity and flexibility. It provides essential validation rules out of the box, supports custom rules and messages, and integrates seamlessly into any PHP project.

## Features

- **Basic Validation Rules**: Includes `required`, `email`, `phone`, `min`, `max`, and more.
- **Custom Rules**: Extend validation with your own rules.
- **Custom Error Messages**: Override default messages globally or per validation.
- **Parameterized Rules**: Define rules with parameters (e.g., `min:8`).
- **Placeholder Support**: Dynamic messages with `:field` and `:param` placeholders.

## Installation

Install via Composer:

```bash
composer require solophp/validator
```

## Usage

```php
<?php

use Solo\Validator;

$validator = new Validator();

$data = [
    'email' => 'user@example.com',
    'username' => 'john_doe',
    'age' => 25,
];

$rules = [
    'email' => 'required|email',
    'username' => 'required|min:3|max:20',
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
- **required**: The field must not be empty.
- **email**: The field must be a valid email address.
- **phone**: he field must be a valid phone number.
- **min:value**: The field must have a minimum length of `value`.
- **max:value**: The field must not exceed `value` in length.
- **filled**: The field must not be empty.
- **integer**: The field must be an integer.
- **string**: The field must be a string.
- **regex**: The field must match the provided regex pattern.
- **numeric**: The field must be a number.
- **array**: The field must be an array.
- **min_value:value**: The field must be at least `value`.
- **max_value:value**: The field must not exceed `value`.

Example:
```php
$rules = [
    'username' => 'required|min:3|max:30',
    'age' => 'required|min:18'
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

- **`fails()`**: Check if validation failed.
- **`errors()`**: Get all validation errors.
- **`passed()`**: Check if validation succeeded.

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

## Contributing

Contributions are welcome! Please open an issue or submit a pull request.

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.

