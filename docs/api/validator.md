# Validator

Main validation class.

```php
use Solo\Validator\Validator;

$validator = new Validator();
```

## Constructor

```php
public function __construct(array $messages = [])
```

Create a new Validator instance with optional global messages.

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `$messages` | `array<string, string>` | `[]` | Global custom messages |

**Example:**

```php
// Default messages
$validator = new Validator();

// With custom messages
$validator = new Validator([
    'required' => 'This field is required.',
    'email' => 'Please enter a valid email.',
]);
```

---

## validate()

```php
public function validate(array $data, array $rules, array $messages = []): array
```

Validate data against rules.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$data` | `array` | Data to validate |
| `$rules` | `array<string, string>` | Validation rules |
| `$messages` | `array<string, string>` | Per-validation messages |

**Returns:** `array<string, list<string>>` — Errors by field

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

$messages = [
    'email.email' => 'Please provide a valid email.',
];

$errors = $validator->validate($data, $rules, $messages);

// [
//     'email' => ['Please provide a valid email.'],
//     'password' => ['The password must be at least 8.'], // :param replaced with rule value
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

**Returns:** `array<string, list<string>>` — Errors grouped by field

```php
$validator->validate($data, $rules);
$errors = $validator->errors();

// [
//     'email' => ['Error 1', 'Error 2'],
//     'password' => ['Error 1'],
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

## ValidatorInterface

The Validator implements `Solo\Contracts\Validator\ValidatorInterface`:

```php
interface ValidatorInterface
{
    public function validate(array $data, array $rules, array $messages = []): array;
    public function fails(): bool;
    public function passed(): bool;
    public function errors(): array;
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
        $this->validator = new Validator([
            'required' => 'This field is required.',
        ]);
        
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
        
        $messages = [
            'email.unique_email' => 'This email is already registered.',
            'password.min' => 'Password must be at least :param characters.',
        ];
        
        $this->validator->validate($data, $rules, $messages);
        
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
