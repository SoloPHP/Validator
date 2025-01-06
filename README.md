# PHP Validator

[![Version](https://img.shields.io/badge/version-1.5.0-blue.svg)](https://github.com/solophp/validator)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)

Simple and flexible validation library for PHP 8.1+ with immutable validation chains.

## Requirements

- PHP >= 8.1
- libphonenumber-for-php

## Installation

```bash
composer require solophp/validator
```

## Usage

```php
use Solo\Validator;

$validator = new Validator();

$validator
    ->validate('email', 'test@example.com')
    ->required()
    ->email();

$validator
    ->validate('phone', '+79991234567')
    ->required()
    ->phone('RU')
    ->pattern('\+7[0-9]{10}');

if ($validator->failed()) {
    $errors = $validator->getErrors();
}
```

## Available Rules

- `required()` - non-empty value
- `unique(bool $isUnique)` - unique value
- `string()` - string type
- `numeric()` - numeric value 
- `pattern(string $regex)` - regex match
- `phone(string $region = 'RU')` - valid phone
- `email()` - valid email
- `positive()` - positive number
- `equal(string|int|float|bool $value)` - equality check

## Custom Messages

Per rule:
```php
$validator
    ->validate('email', 'test@mail.com')
    ->required('Email required')
    ->email('Invalid email');
```

Global:
```php
$validator = new Validator([
    'required' => 'Field {field} required',
    'email' => 'Field {field} must be valid email'
]);
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

MIT