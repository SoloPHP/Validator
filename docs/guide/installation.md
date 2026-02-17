# Installation

## Requirements

- PHP 8.1 or higher
- Composer

## Composer

Install the package via Composer:

```bash
composer require solophp/validator
```

## Dependencies

The package has minimal dependencies:

```json
{
    "require": {
        "php": "^8.1",
        "giggsey/libphonenumber-for-php-lite": "^8.13",
        "solophp/contracts": "^2.0"
    }
}
```

- **libphonenumber-for-php-lite** — For phone number validation
- **solophp/contracts** — Validation interface contract

## Basic Setup

```php
use Solo\Validator\Validator;

// Create validator instance
$validator = new Validator();

// Validate data
$errors = $validator->validate($data, $rules);

// Check result
if ($validator->fails()) {
    // Errors are structured: ['field' => [['rule' => 'required'], ...]]
    foreach ($validator->errors() as $field => $fieldErrors) {
        foreach ($fieldErrors as $error) {
            echo "$field: failed rule '{$error['rule']}'\n";
        }
    }
}
```

## Next Steps

- [Quick Start](/guide/quick-start) — Basic usage examples
- [Validation Rules](/features/rules) — All available rules
