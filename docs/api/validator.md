# Validator

Main validation class.

```php
use Solo\Validator\Validator;

$validator = new Validator();
```

## Constructor

```php
public function __construct()
```

Create a new Validator instance.

**Example:**

```php
$validator = new Validator();
```

---

## validate()

```php
public function validate(array $data, array $rules): array
```

Validate data against rules.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$data` | `array<string, mixed>` | Data to validate |
| `$rules` | `array<string, string>` | Validation rules |

**Returns:** `array<string, list<array{rule: string, params?: string[]}>>` — Structured errors by field

**Example:**

```php
$data = [
    'email' => 'invalid',
    'password' => '123',
];

$rules = [
    'email' => 'required|email',
    'password' => 'required|min:8',
];

$errors = $validator->validate($data, $rules);

// [
//     'email' => [
//         ['rule' => 'email'],
//     ],
//     'password' => [
//         ['rule' => 'min', 'params' => ['8']],
//     ],
// ]
```

---

## fails()

```php
public function fails(): bool
```

Check if validation failed.

**Returns:** `true` if there are errors, `false` otherwise.

```php
$validator->validate($data, $rules);

if ($validator->fails()) {
    // Handle errors
}
```

---

## passed()

```php
public function passed(): bool
```

Check if validation passed.

**Returns:** `true` if no errors, `false` otherwise.

```php
$validator->validate($data, $rules);

if ($validator->passed()) {
    // Process valid data
}
```

---

## errors()

```php
public function errors(): array
```

Get all validation errors.

**Returns:** `array<string, list<array{rule: string, params?: string[]}>>` — Errors grouped by field

```php
$validator->validate($data, $rules);
$errors = $validator->errors();

// [
//     'email' => [
//         ['rule' => 'required'],
//         ['rule' => 'email'],
//     ],
//     'password' => [
//         ['rule' => 'min', 'params' => ['8']],
//     ],
// ]
```

---

## addCustomRule()

```php
public function addCustomRule(string $name, callable $callback): void
```

Register a custom validation rule.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | `string` | Rule name |
| `$callback` | `callable` | Validation callback |

**Callback signature:**

```php
function (mixed $value, ?string $param, array $data): bool
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | `mixed` | Field value |
| `$param` | `?string` | Rule parameter |
| `$data` | `array` | All data |

**Returns:** `true` if valid, `false` if invalid.

**Example:**

```php
$validator->addCustomRule('even', function ($value, $param, $data) {
    return (int)$value % 2 === 0;
});

$validator->addCustomRule('unique', function ($value, $param, $data) {
    // $param = "table,column"
    [$table, $column] = explode(',', $param);
    // Check database...
    return $isUnique;
});

// Usage
$rules = [
    'number' => 'even',
    'email' => 'unique:users,email',
];
```

---

## addRule()

```php
public function addRule(string $name, RuleInterface $rule): void
```

Register a rule implementing `Solo\Validator\RuleInterface`. Unlike `addCustomRule()` (callable returning bool), a `RuleInterface` rule returns the error structure directly — full control over `rule` name and `params`.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | `string` | Rule name |
| `$rule` | `RuleInterface` | Rule instance |

**RuleInterface contract:**

```php
namespace Solo\Validator;

interface RuleInterface
{
    /**
     * @param array<string, mixed> $data Full payload (for cross-field rules)
     * @return array{rule: string, params?: string[]}|null Null = pass; array = error
     */
    public function validate(mixed $value, ?string $parameter, array $data = []): ?array;
}
```

**Example:**

```php
use Solo\Validator\RuleInterface;

final class EvenRule implements RuleInterface
{
    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        return ((int)$value) % 2 === 0 ? null : ['rule' => 'even'];
    }
}

$validator->addRule('even', new EvenRule());
```

Use `addRule()` when a rule needs constructor dependencies (database, cache, services) or when the error shape should differ from the simple `[rule, exploded params]` wrapping that `addCustomRule()` produces. For quick inline checks, `addCustomRule()` with a closure is more concise.

---

## ValidatorInterface

The Validator implements `Solo\Contracts\Validator\ValidatorInterface`:

```php
interface ValidatorInterface
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @return array<string, list<array{rule: string, params?: string[]}>>
     */
    public function validate(array $data, array $rules): array;
}
```

---

## Complete Example

```php
use Solo\Validator\Validator;

class UserController
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();

        // Register custom rules
        $this->validator->addCustomRule('unique_email', function ($value, $param, $data) {
            return !User::where('email', $value)->exists();
        });
    }

    public function register(array $data): array
    {
        $rules = [
            'name' => 'required|min:2|max:50',
            'email' => 'required|email|unique_email',
            'password' => 'required|min:8',
            'age' => 'nullable|integer|min_value:18',
        ];

        $this->validator->validate($data, $rules);

        if ($this->validator->fails()) {
            return [
                'success' => false,
                'errors' => $this->validator->errors(),
            ];
        }

        // Create user...

        return ['success' => true];
    }
}
```
