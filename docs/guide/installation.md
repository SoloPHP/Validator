# Installation

## Requirements

- PHP 8.3 or higher
- Composer

## Composer

Install the package via Composer:

```bash
composer require solophp/validator
```

## Dependencies

```json
{
    "require": {
        "php": "^8.3",
        "solophp/contracts": "^1.3"
    }
}
```

- **solophp/contracts** — the `ValidatorInterface` contract the validator implements

The `phone` rule needs libphonenumber, which is optional:

```bash
composer require giggsey/libphonenumber-for-php-lite
```

Using `phone` without it throws an `InvalidRuleException` that says so.

## Basic Setup

```php
use Solo\Validator\Validator;

$validator = new Validator();

$errors = $validator->validate($data, $rules);

// Errors are structured: ['field' => [['rule' => 'required']], ...]
foreach ($errors as $field => [$error]) {
    echo "$field: failed rule '{$error['rule']}'\n";
}
```

Each field reports at most one error, so `[$error]` destructures it.

## With Request-Handler

The validator implements `Solo\Contracts\Validator\ValidatorInterface`, which [solophp/request-handler](https://github.com/SoloPHP/Request-Handler) 4 expects:

```php
use Solo\RequestHandler\RequestHandler;
use Solo\Validator\Validator;

$handler = new RequestHandler(new Validator([
    'unique' => new UniqueRule($connection),   // your custom rules
]));
```

The handler passes each DTO's `#[Validate]` strings as they are written; the validator reads them with the same grammar as the handler's own rule parser. From Request-Handler 4.1 it also passes the rules of an absent field, without a value — which [`present`](/features/rules#present) needs.

## Next Steps

- [Quick Start](/guide/quick-start) — Basic usage examples
- [Validation Rules](/features/rules) — All available rules
