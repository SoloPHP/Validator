# Solo Validator

Lightweight PHP validation library with custom rules and structured errors.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/solophp/validator.svg)](https://packagist.org/packages/solophp/validator)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

## Features

- **Built-in Rules** — Required, email, phone, uuid, min, max, length, numeric, date, and more
- **Custom Rules** — Extend validation with your own rules via callbacks
- **Structured Errors** — Machine-readable error objects with rule names and parameters
- **Parameterized Rules** — Define rules like `min:8`, `max:100`, `in:a,b,c`
- **Phone Validation** — International phone validation via libphonenumber
- **Lightweight** — Minimal dependencies, PSR-4 compliant

## Installation

```bash
composer require solophp/validator
```

## Quick Example

```php
use Solo\Validator\Validator;

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
    // [
    //     'field' => [
    //         ['rule' => 'email'],
    //         ['rule' => 'min', 'params' => ['3']],
    //     ],
    // ]
} else {
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

## Requirements

- PHP 8.1+

## License

MIT License. See [LICENSE](LICENSE) for details.
