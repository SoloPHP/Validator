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

if ($validator->fails()) {
    print_r($errors);
}
```

## Rule Syntax

Rules are defined as pipe-separated strings:

```php
$rules = [
    'field' => 'rule1|rule2|rule3',
];
```

Rules can have parameters after a colon:

```php
$rules = [
    'username' => 'required|min:3|max:20',
    'age' => 'required|min_value:18|max_value:120',
    'status' => 'required|in:active,inactive,pending',
];
```

## Checking Results

```php
$validator->validate($data, $rules);

// Check if validation failed
if ($validator->fails()) {
    $errors = $validator->errors();
}

// Check if validation passed
if ($validator->passed()) {
    // Process data
}
```

## Error Structure

Errors are returned as structured arrays with rule name and optional parameters:

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
- `params` — (optional) array of rule parameters

This format is ideal for building custom error messages on the client side or for i18n/l10n.

---

## Form Validation Example

```php
$data = $_POST;

$rules = [
    'name' => 'required|min:2|max:50',
    'email' => 'required|email',
    'phone' => 'phone:US',
    'age' => 'required|integer|min_value:18',
    'password' => 'required|min:8',
    'terms' => 'required|boolean',
];

$errors = $validator->validate($data, $rules);

if ($validator->fails()) {
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

if ($validator->fails()) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}
```

## Nullable Fields

Use `nullable` to allow empty values:

```php
$rules = [
    'bio' => 'nullable|max:500',
    'website' => 'nullable|regex:/^https?:\/\//',
];

// These will pass validation:
$data = ['bio' => null, 'website' => ''];
$data = ['bio' => 'Hello!', 'website' => 'https://example.com'];
```

## Next Steps

- [Validation Rules](/features/rules) — Complete rules reference
- [Custom Rules](/features/custom-rules) — Create your own rules
