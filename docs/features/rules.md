# Validation Rules

Complete reference of all built-in validation rules.

## String Rules

### required

Field must not be empty, null, or an empty array.

```php
$rules = ['name' => 'required'];
```

### filled

Field must not be empty if present.

```php
$rules = ['description' => 'filled'];
```

### string

Field must be a string.

```php
$rules = ['title' => 'string'];
```

### min

Field must have at least X characters.

```php
$rules = ['password' => 'min:8'];
```

### max

Field must not exceed X characters.

```php
$rules = ['username' => 'max:20'];
```

### length

Field must be exactly X characters.

```php
$rules = ['pin' => 'length:4'];
```

### regex

Field must match the regex pattern.

```php
$rules = [
    'slug' => 'regex:/^[a-z0-9-]+$/',
    'code' => 'regex:/^[A-Z]{3}[0-9]{4}$/',
];
```

---

## Numeric Rules

### integer

Field must be an integer.

```php
$rules = ['age' => 'integer'];
```

### numeric

Field must be a number (integer or float).

```php
$rules = ['price' => 'numeric'];
```

### min_value

Field must be at least X (numeric comparison).

```php
$rules = ['age' => 'min_value:18'];
```

### max_value

Field must not exceed X (numeric comparison).

```php
$rules = ['quantity' => 'max_value:100'];
```

---

## Format Rules

### email

Field must be a valid email address.

```php
$rules = ['email' => 'email'];
```

### phone

Field must be a valid phone number. Optionally specify region code.

```php
$rules = [
    'phone' => 'phone',        // Any valid number
    'phone' => 'phone:US',     // US number
    'phone' => 'phone:UA',     // Ukrainian number
];
```

Uses [libphonenumber](https://github.com/giggsey/libphonenumber-for-php) for validation.

### date

Field must be a valid date. Optionally specify format.

```php
$rules = [
    'created_at' => 'date',              // Any parseable date
    'birth_date' => 'date:Y-m-d',        // Specific format
    'event_time' => 'date:Y-m-d H:i:s',  // With time
];
```

Also accepts Unix timestamps:

```php
$data = ['timestamp' => 1705334400];
$rules = ['timestamp' => 'date'];  // Valid
```

---

## Type Rules

### boolean

Field must be a boolean value. Accepts:
- `true`, `false`
- `1`, `0`
- `'1'`, `'0'`
- `'true'`, `'false'`
- `'yes'`, `'no'`
- `'on'`, `'off'`

```php
$rules = ['is_active' => 'boolean'];

// All valid:
$data = ['is_active' => true];
$data = ['is_active' => 1];
$data = ['is_active' => 'yes'];
$data = ['is_active' => 'on'];
```

### array

Field must be an array. Optionally specify allowed keys.

```php
$rules = [
    'tags' => 'array',                      // Any array
    'user' => 'array:name,email,phone',     // Only these keys allowed
];
```

Example with key validation:

```php
// Valid
$data = ['user' => ['name' => 'John', 'email' => 'john@example.com']];

// Invalid - 'password' key not allowed
$data = ['user' => ['name' => 'John', 'password' => 'secret']];
```

---

## Choice Rules

### in

Field must be one of the specified values.

```php
$rules = [
    'status' => 'in:active,inactive,pending',
    'role' => 'in:admin,user,guest',
];
```

Also works with arrays:

```php
$rules = ['tags' => 'in:php,javascript,python,go'];

// Valid - all values are in the allowed list
$data = ['tags' => ['php', 'javascript']];

// Invalid - 'ruby' is not allowed
$data = ['tags' => ['php', 'ruby']];
```

### nullable

Field can be null or empty. Skips all other rules if empty.

```php
$rules = [
    'middle_name' => 'nullable|min:2',
    'website' => 'nullable|regex:/^https?:\/\//',
];

// Both valid:
$data = ['middle_name' => null];
$data = ['middle_name' => 'James'];
```

---

## Rules Reference Table

| Rule | Parameters | Description |
|------|------------|-------------|
| `required` | — | Must not be empty |
| `filled` | — | Must not be empty if present |
| `string` | — | Must be a string |
| `integer` | — | Must be an integer |
| `numeric` | — | Must be a number |
| `boolean` | — | Must be true/false |
| `array` | `keys` (optional) | Must be an array |
| `email` | — | Must be valid email |
| `phone` | `region` (optional) | Must be valid phone |
| `date` | `format` (optional) | Must be valid date |
| `min` | `length` | Minimum string length |
| `max` | `length` | Maximum string length |
| `length` | `length` | Exact string length |
| `min_value` | `value` | Minimum numeric value |
| `max_value` | `value` | Maximum numeric value |
| `in` | `values` | Must be in list |
| `regex` | `pattern` | Must match pattern |
| `nullable` | — | Allow null/empty |

---

## Combining Rules

Rules are processed left to right:

```php
$rules = [
    // Required, then validate format
    'email' => 'required|email',
    
    // Required, then check length
    'password' => 'required|min:8|max:100',
    
    // Required, numeric, then range
    'age' => 'required|integer|min_value:18|max_value:120',
    
    // Optional, but if present must be valid
    'website' => 'nullable|regex:/^https?:\/\//',
];
```

::: tip
Place `nullable` first to skip validation for empty values.
:::
