да, вы правы. Давайте исправлю имя в ридми:

# PHP Validator

A simple and flexible validation library for PHP 8.1 or higher with chainable rules.

## Requirements

- PHP >= 8.1
- libphonenumber-for-php

## Installation

```bash
composer require solophp/validator
```

## Basic Usage

```php
use Solo\Validator;

$validator = new Validator();

$data = [
    'email' => 'test@example.com',
    'name' => 'John Doe',
    'age' => 25,
    'phone' => '+79991234567'
];

$validator->collect($data);

$validator
    ->validate('email')
    ->required()
    ->email();

$validator
    ->validate('phone')
    ->required()
    ->phone('RU')
    ->pattern('\+7[0-9]{10}');

if ($validator->failed()) {
    $errors = $validator->getErrors();
    // Handle errors
}
```

## Available Rules

- `required()` - checks if value is not empty
- `unique(bool $isUnique)` - checks if value is unique
- `string()` - checks if value is string
- `numeric()` - checks if value is numeric
- `pattern(string $regex)` - validates if value matches the pattern
- `phone(string $region = 'RU')` - validates if value is a valid phone number
- `email()` - checks if value is valid email address
- `positive()` - checks if value is positive number
- `equal(string|int|float|bool $value)` - checks if value equals to provided value

## Custom Error Messages

You can pass custom error messages to any validation rule:

```php
$validator
    ->validate('email')
    ->required('Email field is required')
    ->email('Please enter a valid email address');
```

## Global Custom Messages

You can override default error messages by passing an array to the constructor:

```php
$customMessages = [
    'required' => 'Field {field} must not be empty',
    'email' => 'Field {field} must be a valid email'
];

$validator = new Validator($customMessages);
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

MIT