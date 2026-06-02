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

Field must have at least X characters, or — when the value is an array — at least X elements.

```php
$rules = [
    'password' => 'min:8',   // string: at least 8 chars
    'tags' => 'min:1',       // array: at least 1 element
];
```

### max

Field must not exceed X characters, or — when the value is an array — must not contain more than X elements.

```php
$rules = [
    'username' => 'max:20',  // string: at most 20 chars
    'tags' => 'max:5',       // array: at most 5 elements
];
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

### uuid

Field must be a valid UUID (v1-v5).

```php
$rules = ['id' => 'uuid'];

// Valid
$data = ['id' => '550e8400-e29b-41d4-a716-446655440000'];

// Invalid
$data = ['id' => 'not-a-uuid'];
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

Marks a field as optional. When the value is **blank** — `null`, an empty string `''`, or an empty array `[]` — every other rule on that field is skipped, so blank input passes.

```php
$rules = [
    'middle_name' => 'nullable|min:2',
    'website' => 'nullable|regex:/^https?:\/\//',
];

// All valid:
$data = ['middle_name' => null];      // absent / null
$data = ['middle_name' => ''];        // empty string
$data = ['middle_name' => 'James'];   // present and validated
```

Only blank values are skipped. Non-blank "falsy" values such as `'0'`, `0`, or `false` are **not** treated as blank and are still validated by the remaining rules.

The presence rules `required` and `filled` are the exception — they always run, even alongside `nullable`, since their whole job is to assert presence. Combining them with `nullable` is contradictory (`required|nullable` still fails on a blank value). Any custom rule that needs to run on blank values can opt in by implementing [`PresenceRule`](/features/custom-rules#presence-rules-presencerule).

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
| `uuid` | — | Must be valid UUID |
| `min` | `length` | Minimum string length or array element count |
| `max` | `length` | Maximum string length or array element count |
| `length` | `length` | Exact string length |
| `min_value` | `value` | Minimum numeric value |
| `max_value` | `value` | Maximum numeric value |
| `in` | `values` | Must be in list |
| `regex` | `pattern` | Must match pattern |
| `nullable` | — | Allow blank (null / `''` / `[]`); skips other rules |

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
`nullable` applies to the whole field regardless of its position in the chain, but placing it first reads best. It skips the remaining rules only when the value is blank (`null`, `''`, or `[]`); `required` and `filled` still run.
:::
