# Validation Rules

Complete reference of all built-in validation rules.

Rules that read a value as a string — `min`, `max`, `length`, `regex`, `in` — accept a string, an int, a finite float or a `Stringable` object. Any other value (a bool, an array where the rule wants a string, an object without `__toString()`) fails the rule; it never raises a PHP warning.

## Presence Rules

Presence rules run for every field: also when it has no value — it is absent or `null` — and when it is blank under `nullable`. Every other rule is skipped in those cases.

A value is **filled** unless it is `null`, `''`, `[]` or a string of only whitespace.

### required

Field must be filled.

```php
$rules = ['name' => 'required'];
```

### filled

Field may be absent; when present, it must be filled.

```php
$rules = ['description' => 'filled'];
```

### present

Field must be in the data; any value passes, `null`, `''` and `[]` included.

```php
$rules = ['tags' => 'present|array'];

// Valid:   ['tags' => []]
// Invalid: []  — the key is missing
```

For a value the client has to give explicitly even when it is empty: an empty list that clears a collection must not be confused with a list nobody sent.

::: warning With Request-Handler
`present` needs [solophp/request-handler](https://github.com/SoloPHP/Request-Handler) 4.1 or later. Earlier versions hand an absent field to the validator only when one of its rules starts with `required`, so `present` alone never sees the missing key.
:::

### required_if

Field must be filled when another field equals one of the given values.

```php
$rules = [
    'company' => 'required_if:type,company',
    'reason' => 'required_if:status,rejected,cancelled',
];
```

The other field's value is compared with how the rule string spells it:

| Rule spells | Matches |
|---|---|
| `true` / `false` | what the `boolean` rule reads as such: `true`, `1`, `'1'`, `'yes'`, `'on'`… |
| `null` | `null`, or the field is absent |
| `1` / `0` | also `true` / `false` |
| anything else | the value's string form, exactly |

### required_unless

Field must be filled unless another field equals one of the given values.

```php
$rules = ['tax_id' => 'required_unless:country,US'];
```

### required_with / required_with_all

Field must be filled when any (`required_with`) or all (`required_with_all`) of the listed fields are filled.

```php
$rules = [
    'email' => 'required_with:newsletter',
    'card_cvc' => 'required_with_all:card_number,card_expiry',
];
```

### required_without / required_without_all

Field must be filled when any (`required_without`) or all (`required_without_all`) of the listed fields are not filled.

```php
$rules = [
    'email' => 'required_without:phone',
    'contact' => 'required_without_all:email,phone',
];
```

### nullable

Not a rule but a flag: when the value is **blank** — `''` or `[]` — only presence rules run, so blank input passes. `null` needs no flag: it is no value, like a missing field.

```php
$rules = [
    'middle_name' => 'nullable|min:2',
    'website' => 'nullable|regex:/^https?:\/\//',
];

// All valid:
$data = ['middle_name' => null];
$data = ['middle_name' => ''];
$data = ['middle_name' => 'James'];
```

Only blank values are skipped. `'0'`, `0`, `false` and whitespace such as `'   '` are not blank and are validated by the remaining rules.

`nullable` combines with conditional presence rules: `nullable|required_if:type,company` accepts a blank value unless `type` is `company`. With `required` it is contradictory — `nullable|required` still fails on a blank value.

---

## Prohibition Rules

A blank or absent field passes them, so they need not be presence rules.

### prohibited_if

Field must not be filled when another field equals one of the given values.

```php
$rules = [
    'company' => 'prohibited_if:type,individual',
    'reason' => 'prohibited_if:status,approved,draft',
];
```

The other field is compared the way [`required_if`](#required-if) compares it. Together they describe a field only one kind of record has:

```php
$rules = ['company' => 'nullable|string|required_if:type,company|prohibited_if:type,individual'];
```

### prohibited_unless

Field must not be filled unless another field equals one of the given values.

```php
$rules = ['discount_reason' => 'prohibited_unless:discount,true'];
```

---

## Type Rules

### string

Field must be a string.

```php
$rules = ['title' => 'string'];
```

### integer

Field must be an integer: an int, a whole float a float holds exactly (`5.0`), or a string PHP reads as an int (`'42'`, `'-5'`). Never a bool.

```php
$rules = ['age' => 'integer'];
```

### numeric

Field must be a finite number: an int, a float or a numeric string (`'9.99'`, `'1e3'`). Never a bool, `INF`, `NAN`, or `'1e400'` (which overflows to `INF`).

```php
$rules = ['price' => 'numeric'];
```

### boolean

Field must be a boolean value. Accepts:
- `true`, `false`
- `1`, `0`
- `'1'`, `'0'`, `'true'`, `'false'`, `'yes'`, `'no'`, `'on'`, `'off'` in any case

```php
$rules = ['is_active' => 'boolean'];
```

### array

Field must be an array. Optionally limit its keys.

```php
$rules = [
    'tags' => 'array',                      // Any array
    'user' => 'array:name,email,phone',     // Only these keys allowed
];

// Fails with ['rule' => 'array_keys', 'params' => ['name', 'email', 'phone']]
$data = ['user' => ['name' => 'John', 'password' => 'secret']];
```

The integer, numeric and boolean rules accept exactly what Request-Handler 4 converts into an `int`, `float` or `bool` property.

---

## Size Rules

Sizes count the elements of an array, or the characters (multibyte-aware) of a string, int, float or `Stringable`; `''` and `[]` have size 0. A bool or another object fails. A `null` is no value: size rules never see it.

::: warning Numbers are sized by their characters
`max:100` accepts 99999999 and `min:1` accepts 0: they count digits. Bound a number's value with [`min_value` / `max_value`](#numeric-value-rules).
:::

### min

```php
$rules = [
    'password' => 'min:8',   // string: at least 8 characters
    'tags' => 'min:1',       // array: at least 1 element
];
```

### max

```php
$rules = [
    'username' => 'max:20',  // string: at most 20 characters
    'tags' => 'max:5',       // array: at most 5 elements
];
```

### length

```php
$rules = [
    'pin' => 'length:4',     // string: exactly 4 characters
    'rgb' => 'length:3',     // array: exactly 3 elements
];
```

The parameter must be one non-negative integer.

---

## Numeric Value Rules

### min_value

Field must be a number (as `numeric` reads it) of at least X.

```php
$rules = ['age' => 'min_value:18'];
```

### max_value

Field must be a number (as `numeric` reads it) of at most X.

```php
$rules = ['quantity' => 'max_value:100'];
```

A value that is not a number fails the rule with its own error (`min_value` / `max_value`). The parameter must be one finite number.

---

## Format Rules

### email

Field must be a string holding a valid email address.

```php
$rules = ['email' => 'email'];
```

### phone

Field must be a valid phone number. Optionally specify a region code (any case).

```php
$rules = [
    'phone' => 'phone',        // A valid number in international format (+14155552671)
    'phone' => 'phone:US',     // A number without a country code is read as a US number
];
```

Input that cannot be parsed — a national number without a region, an unknown country code — fails like any other invalid number. With a region, the error carries it: `['rule' => 'phone', 'params' => ['US']]`.

Needs [libphonenumber](https://github.com/giggsey/libphonenumber-for-php): `composer require giggsey/libphonenumber-for-php-lite`.

### date

Field must be a valid date. Optionally specify a format.

```php
$rules = [
    'created_at' => 'date',               // Any date PHP can read
    'birth_date' => 'date:Y-m-d',         // Exactly this format
    'event_time' => 'date:Y-m-d H:i:s',
    'published' => 'date:D, d M Y',       // Commas are fine
];
```

Without a format, `date` accepts an int (a Unix timestamp), a digit string that is one, or a string PHP parses as a date (`'2024-01-15'`, `'next monday'`). It rejects an impossible date (`'2024-02-31'`), digits PHP would read as a time of day (`'1230'`), an empty string and floats.

With a format, the value — a string, or an int read as one — must be written exactly in it: `'2024-1-5'` fails `date:Y-m-d` (use `date:Y-n-j` for unpadded numbers), and impossible dates fail. Formats with parse-only characters (`!`, `|`, `?`, `*`, `#`, `+`) cannot be reproduced, so for them a successful parse is enough. The error is `['rule' => 'date_format', 'params' => [format]]`.

### uuid

Field must be a UUID string: 8-4-4-4-12 hexadecimal digits, any case.

```php
$rules = ['id' => 'uuid'];

// Valid
$data = ['id' => '550e8400-e29b-41d4-a716-446655440000'];
```

### regex

Field must match the pattern.

```php
$rules = [
    'slug' => 'regex:/^[a-z0-9-]+$/',
    'code' => 'regex:/^[A-Z]{3}[0-9]{4}$/',
];
```

An invalid pattern throws when the rule string is first used. The pattern can contain commas but not `|`, which separates rules — register a [custom rule](/features/custom-rules) for an alternation. The error has no parameters, so the pattern stays private.

---

## Choice Rules

### in

Field must be one of the listed values; for an array value, every element must be.

```php
$rules = [
    'status' => 'in:active,inactive,pending',
    'tags' => 'in:php,javascript,python,go',
];

// Valid
$data = ['tags' => ['php', 'javascript']];

// Invalid - 'ruby' is not allowed
$data = ['tags' => ['php', 'ruby']];
```

Values are compared by their string form, exactly. A bool, a nested array or an object without `__toString()` never matches. A `null` is no value: `in` never sees it — add `required` or `filled` to refuse it.

---

## Rules Reference Table

| Rule | Parameters | Description |
|------|------------|-------------|
| `required` | — | Must be filled |
| `filled` | — | Must be filled if present |
| `present` | — | Must be in the data, any value |
| `required_if` | `field,value,…` | Required when another field has one of the values |
| `required_unless` | `field,value,…` | Required unless another field has one of the values |
| `required_with` | `field,…` | Required when any listed field is filled |
| `required_with_all` | `field,…` | Required when all listed fields are filled |
| `required_without` | `field,…` | Required when any listed field is not filled |
| `required_without_all` | `field,…` | Required when no listed field is filled |
| `prohibited_if` | `field,value,…` | Must not be filled when another field has one of the values |
| `prohibited_unless` | `field,value,…` | Must not be filled unless another field has one of the values |
| `nullable` | — | Blank (`''` / `[]`) runs presence rules only, as `null` always does |
| `string` | — | Must be a string |
| `integer` | — | Must be an integer |
| `numeric` | — | Must be a finite number |
| `boolean` | — | Must be boolean-like |
| `array` | `keys` (optional) | Must be an array |
| `min` | `size` | Minimum characters or elements |
| `max` | `size` | Maximum characters or elements |
| `length` | `size` | Exact characters or elements |
| `min_value` | `number` | Minimum numeric value |
| `max_value` | `number` | Maximum numeric value |
| `email` | — | Must be a valid email |
| `phone` | `region` (optional) | Must be a valid phone number |
| `date` | `format` (optional) | Must be a valid date |
| `uuid` | — | Must be a UUID |
| `regex` | `pattern` | Must match the pattern |
| `in` | `values` | Must be one of the values |

---

## Combining Rules

Rules run left to right, and the first failing rule ends the field — each field reports at most one error:

```php
$rules = [
    'email' => 'required|email',                         // missing → required
    'password' => 'required|min:8|max:100',
    'age' => 'required|integer|min_value:18|max_value:120', // 'abc' → integer
    'website' => 'nullable|regex:/^https?:\/\//',
];
```

::: tip
`nullable` applies to the whole field wherever it stands in the chain, but placing it first reads best.
:::
