# Quick Start

## Basic Validation

```php
use Solo\Validator\Validator;

$validator = new Validator();

$data = [
    'email' => 'user@example.com',
    'password' => 'secret123',
];

$rules = [
    'email' => 'required|email',
    'password' => 'required|min:8',
];

$errors = $validator->validate($data, $rules);

if ($errors !== []) {
    print_r($errors);
}
```

`validate()` returns the errors and keeps nothing: the same instance can validate any number of payloads, in any order, from any request.

## Rule Syntax

Rules are defined as pipe-separated strings:

```php
$rules = [
    'field' => 'rule1|rule2|rule3',
];
```

Whitespace around each rule is ignored: `'required | email'` is the same as `'required|email'`.

Rules can have parameters after a colon, separated by commas:

```php
$rules = [
    'username' => 'required|min:3|max:20',
    'age' => 'required|min_value:18|max_value:120',
    'status' => 'required|in:active,inactive,pending',
];
```

A parameter cannot contain `|`, which separates rules. Commas are fine in a `regex` pattern or a `date` format.

## Configuration Errors

A rule string is checked the first time it is used, whatever the input:

```php
$validator->validate($data, ['email' => 'requird|email']);
// InvalidRuleException: Unknown validation rule 'requird' in 'requird|email'.

$validator->validate($data, ['name' => 'min:abc']);
// InvalidRuleException: Invalid rule 'min:abc': expects one non-negative integer.
```

A typo never silently disables validation.

## Error Structure

Each failing field gets one error, from its first failing rule:

```php
[
    'email' => [
        ['rule' => 'email'],
    ],
    'password' => [
        ['rule' => 'min', 'params' => ['8']],
    ],
    'status' => [
        ['rule' => 'in', 'params' => ['active', 'inactive', 'pending']],
    ],
]
```

Each error contains:
- `rule` — the name of the failed validation rule
- `params` — (optional) the rule's parameters as written

This format is ideal for building error messages on the client side or for i18n/l10n.

## Which Rules Run

| The field is… | Rules that run |
|---|---|
| absent from the data | presence rules only (`required`, `filled`, `required_if`, …) |
| blank (`null`, `''`, `[]`) and the rules contain `nullable` | presence rules only |
| anything else | every rule, until the first one fails |

So `'email'` alone accepts a missing field; add `required` to demand it. A present `null` without `nullable` is validated like any other value.

## Nullable Fields

Use `nullable` to accept blank values:

```php
$rules = [
    'bio' => 'nullable|max:500',
    'website' => 'nullable|regex:/^https?:\/\//',
];

// These pass:
$data = ['bio' => null, 'website' => ''];
$data = ['bio' => 'Hello!', 'website' => 'https://example.com'];

// This fails: whitespace is not blank, and '   ' is not a URL.
$data = ['website' => '   '];
```

---

## Form Validation Example

```php
$rules = [
    'name' => 'required|min:2|max:50',
    'email' => 'required|email',
    'phone' => 'nullable|phone:US',
    'age' => 'required|integer|min_value:18',
    'password' => 'required|min:8',
    'terms' => 'required|boolean',
];

$errors = $validator->validate($_POST, $rules);

if ($errors !== []) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Process valid data
```

## API Validation Example

```php
$data = json_decode(file_get_contents('php://input'), true);

$rules = [
    'title' => 'required|min:3|max:200',
    'content' => 'required|min:10',
    'status' => 'required|in:draft,published,archived',
    'tags' => 'array',
    'metadata' => 'nullable|array:author,source',
    'id' => 'nullable|uuid',
];

$errors = $validator->validate($data, $rules);

if ($errors !== []) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}
```

## Next Steps

- [Validation Rules](/features/rules) — Complete rules reference
- [Custom Rules](/features/custom-rules) — Create your own rules
