# Custom Messages

Override default error messages globally or per validation.

## Message Priority

Messages are resolved in this order:

1. **Field-specific** — `field.rule` (e.g., `email.required`)
2. **Rule-specific** — `rule` (e.g., `required`)
3. **Default message** — Built-in message

```php
$messages = [
    'email.required' => 'Email address is required.',    // Highest priority
    'required' => 'This field cannot be empty.',         // Lower priority
];
```

---

## Global Messages

Pass messages to the constructor for all validations:

```php
$messages = [
    'required' => 'This field is required.',
    'email' => 'Please enter a valid email address.',
    'min' => 'Must be at least :param characters.',
];

$validator = new Validator($messages);

// These messages will be used for all validate() calls
```

## Per-Validation Messages

Pass messages as the third parameter to `validate()`:

```php
$rules = [
    'email' => 'required|email',
    'password' => 'required|min:8',
];

$messages = [
    'email.required' => 'We need your email to continue.',
    'email.email' => 'This doesn\'t look like a valid email.',
    'password.min' => 'Password should be at least :param characters for security.',
];

$errors = $validator->validate($data, $rules, $messages);
```

---

## Placeholders

Use placeholders for dynamic message content:

| Placeholder | Description |
|-------------|-------------|
| `:field` | The field name |
| `:param` | The rule parameter |

### Examples

```php
$messages = [
    'min' => 'The :field must be at least :param characters.',
    'max' => 'The :field cannot exceed :param characters.',
    'min_value' => 'The :field must be at least :param.',
    'in' => 'The :field must be one of: :param.',
];

// With data:
$data = ['username' => 'ab'];
$rules = ['username' => 'min:3'];

// Result: "The username must be at least 3 characters."
```

### Field-Specific with Placeholders

```php
$messages = [
    'password.min' => 'Your password needs at least :param characters.',
    'age.min_value' => 'You must be at least :param years old.',
];
```

---

## Default Messages

Built-in default messages:

```php
[
    'required' => 'The :field field is required.',
    'email' => 'The :field must be a valid email address.',
    'phone' => 'The :field must be a valid phone number.',
    'length' => 'The :field must be exactly :param characters.',
    'min' => 'The :field must be at least :param.',
    'max' => 'The :field must not exceed :param.',
    'filled' => 'The :field must not be empty.',
    'integer' => 'The :field must be an integer.',
    'string' => 'The :field must be a string.',
    'regex' => 'The :field format is invalid.',
    'numeric' => 'The :field must be a number.',
    'array' => 'The :field must be an array.',
    'array_keys' => 'The :field contains invalid keys. Allowed: :param.',
    'boolean' => 'The :field must be true or false.',
    'min_value' => 'The :field must be at least :param.',
    'max_value' => 'The :field must not exceed :param.',
    'in' => 'The :field must be one of: :param.',
    'date' => 'The :field must be a valid date.',
    'date_format' => 'The :field must match the format :param.', // Used by date:format rule
]
```

::: tip
The `date_format` message is used internally by the `date` rule when a format is specified (e.g., `date:Y-m-d`). It's not a separate rule.
:::

---

## Complete Example

```php
use Solo\Validator\Validator;

// Global messages for common rules
$globalMessages = [
    'required' => 'This field is required.',
    'email' => 'Please provide a valid email address.',
];

$validator = new Validator($globalMessages);

// Registration form
$data = $_POST;

$rules = [
    'name' => 'required|min:2|max:50',
    'email' => 'required|email',
    'password' => 'required|min:8',
    'age' => 'required|integer|min_value:18',
    'terms' => 'required|boolean',
];

// Form-specific messages
$formMessages = [
    'name.min' => 'Name must be at least :param characters.',
    'name.max' => 'Name cannot exceed :param characters.',
    'password.min' => 'For security, password must be at least :param characters.',
    'age.min_value' => 'You must be at least :param years old to register.',
    'terms.required' => 'You must accept the terms and conditions.',
];

$errors = $validator->validate($data, $rules, $formMessages);

if ($validator->fails()) {
    foreach ($errors as $field => $messages) {
        foreach ($messages as $message) {
            echo "<p class='error'>$message</p>";
        }
    }
}
```

---

## Localization

For multi-language support, load messages from language files:

```php
// lang/en/validation.php
return [
    'required' => 'The :field field is required.',
    'email' => 'The :field must be a valid email address.',
    // ...
];

// lang/uk/validation.php
return [
    'required' => 'Поле :field є обов\'язковим.',
    'email' => 'Поле :field повинно бути дійсною email адресою.',
    // ...
];

// Usage
$lang = $_SESSION['locale'] ?? 'en';
$messages = require "lang/{$lang}/validation.php";

$validator = new Validator($messages);
```
