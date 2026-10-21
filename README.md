# Solo Validator

Lightweight PHP validation library: rule strings compiled once, strict about configuration, one structured error per field.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/solophp/validator.svg)](https://packagist.org/packages/solophp/validator)
[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

## Features

- **Built-in Rules** — required, required_if / with / without, prohibited_if, present, email, phone, uuid, date, min, max, in, regex and more
- **Fails Fast** — an unknown rule or a bad parameter throws on first use instead of silently validating nothing
- **Structured Errors** — at most one machine-readable error per field: the rule name and its parameters
- **Custom Rules** — callables or classes, registered in the constructor
- **Stateless** — `validate()` returns the errors, so one instance can be shared across requests and workers
- **Request-Handler ready** — the validator behind [solophp/request-handler](https://github.com/SoloPHP/Request-Handler) 4: the same rule grammar, the same reading of types

## Installation

```bash
composer require solophp/validator
```

The `phone` rule also needs libphonenumber:

```bash
composer require giggsey/libphonenumber-for-php-lite
```

## Quick Example

```php
use Solo\Validator\Validator;

$validator = new Validator();

$errors = $validator->validate(
    ['email' => 'user@example', 'username' => 'jo', 'age' => '25'],
    [
        'email' => 'required|email',
        'username' => 'required|min:3|max:20',
        'age' => 'integer|min_value:18',
    ],
);

// [
//     'email' => [['rule' => 'email']],
//     'username' => [['rule' => 'min', 'params' => ['3']]],
// ]

if ($errors === []) {
    echo "Validation passed!";
}
```

## Documentation

**[Full Documentation](https://solophp.github.io/Validator/)**

- [Installation](https://solophp.github.io/Validator/guide/installation)
- [Quick Start](https://solophp.github.io/Validator/guide/quick-start)
- [Validation Rules](https://solophp.github.io/Validator/features/rules)
- [Custom Rules](https://solophp.github.io/Validator/features/custom-rules)
- [API Reference](https://solophp.github.io/Validator/api/validator)

Upgrading from 3.x? See [UPGRADE.md](UPGRADE.md).

## Requirements

- PHP 8.3+

## License

MIT License. See [LICENSE](LICENSE) for details.
