# Custom Rules

Extend validation with your own rules.

## Adding Custom Rules

Use `addCustomRule()` to register a custom validation rule:

```php
$validator->addCustomRule('rule_name', function ($value, $param, $data) {
    // Return true if valid, false if invalid
    return $value === 'expected';
});
```

**Callback parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | `mixed` | The field value being validated |
| `$param` | `?string` | Parameter passed to the rule (after `:`) |
| `$data` | `array` | All data being validated |

---

## Examples

### Even Number

```php
$validator->addCustomRule('even', function ($value, $param, $data) {
    return (int)$value % 2 === 0;
});

$rules = ['number' => 'even'];

$validator->validate(['number' => 3], $rules);
// Error: [['rule' => 'even']]

$validator->validate(['number' => 4], $rules);
// Passes
```

### Unique (Database Check)

```php
$validator->addCustomRule('unique', function ($value, $param, $data) {
    // $param = "users,email" → table and column
    [$table, $column] = explode(',', $param);

    global $db;
    $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
    $stmt->execute([$value]);

    return $stmt->fetchColumn() === 0;
});

$rules = ['email' => 'required|email|unique:users,email'];
```

### Confirmed (Password Confirmation)

```php
$validator->addCustomRule('confirmed', function ($value, $param, $data) {
    $confirmField = $param ?: 'password_confirmation';
    return isset($data[$confirmField]) && $value === $data[$confirmField];
});

$rules = [
    'password' => 'required|min:8|confirmed',
];

$data = [
    'password' => 'secret123',
    'password_confirmation' => 'secret456',  // Doesn't match
];

$validator->validate($data, $rules);
// Error: [['rule' => 'confirmed']]
```

### Strong Password

```php
$validator->addCustomRule('strong_password', function ($value, $param, $data) {
    // At least one uppercase, one lowercase, one digit
    return preg_match('/[A-Z]/', $value)
        && preg_match('/[a-z]/', $value)
        && preg_match('/[0-9]/', $value);
});

$rules = ['password' => 'required|min:8|strong_password'];
```

### Depends On Another Field

```php
$validator->addCustomRule('required_if', function ($value, $param, $data) {
    // $param = "field,expected_value"
    [$field, $expected] = explode(',', $param);

    // Only required if other field has expected value
    if (($data[$field] ?? null) !== $expected) {
        return true;  // Not required, skip
    }

    return $value !== null && $value !== '';
});

$rules = [
    'payment_method' => 'required|in:card,bank',
    'card_number' => 'required_if:payment_method,card',
];

$data = ['payment_method' => 'card', 'card_number' => ''];
// Error on card_number: [['rule' => 'required_if', 'params' => ['payment_method', 'card']]]
```

---

## With Parameters

Custom rules can accept parameters via the colon syntax:

```php
$validator->addCustomRule('divisible_by', function ($value, $param, $data) {
    $divisor = (int)$param;
    return (int)$value % $divisor === 0;
});

$rules = ['quantity' => 'divisible_by:5'];

$validator->validate(['quantity' => 17], $rules);
// Error: [['rule' => 'divisible_by', 'params' => ['5']]]
```

---

## Using Full Data Context

The `$data` parameter gives access to all fields:

```php
$validator->addCustomRule('different', function ($value, $param, $data) {
    return $value !== ($data[$param] ?? null);
});

$rules = [
    'old_password' => 'required',
    'new_password' => 'required|min:8|different:old_password',
];
```

---

## Reusable Validator Setup

For complex applications, register rules in a factory or service:

```php
class ValidatorFactory
{
    public static function create(): Validator
    {
        $validator = new Validator();

        $validator->addCustomRule('unique', [self::class, 'validateUnique']);
        $validator->addCustomRule('exists', [self::class, 'validateExists']);
        $validator->addCustomRule('confirmed', [self::class, 'validateConfirmed']);

        return $validator;
    }

    private static function validateUnique($value, $param, $data): bool
    {
        // Implementation
    }

    private static function validateExists($value, $param, $data): bool
    {
        // Implementation
    }

    private static function validateConfirmed($value, $param, $data): bool
    {
        $confirmField = $param ?: array_key_first($data) . '_confirmation';
        return $value === ($data[$confirmField] ?? null);
    }
}
```
