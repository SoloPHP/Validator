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
        "solophp/contracts": "^1.0"
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
    // Handle errors
    foreach ($validator->errors() as $field => $messages) {
        echo "$field: " . implode(', ', $messages) . "\n";
    }
}
```

## With Custom Messages

```php
// Define global custom messages
$messages = [
    'required' => 'This field is required.',
    'email' => 'Please provide a valid email.',
];

// Pass to constructor
$validator = new Validator($messages);
```

## Next Steps

- [Quick Start](/guide/quick-start) — Basic usage examples
- [Validation Rules](/features/rules) — All available rules
