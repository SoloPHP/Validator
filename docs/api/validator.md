# Validator

Main validation class. Implements `Solo\Contracts\Validator\ValidatorInterface`.

```php
use Solo\Validator\Validator;

$validator = new Validator();
```

## Constructor

```php
public function __construct(array $rules = [])
```

Create a validator, optionally with custom rules.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rules` | `array<string, RuleInterface\|callable>` | Custom rules by name; a built-in name overrides that rule |

**Throws:**
- `InvalidRuleException` — a name no rule string can refer to (`nullable`, empty, containing `|` or `:`, or with surrounding whitespace)
- `TypeError` — a rule that is neither a `RuleInterface` nor a callable

**Example:**

```php
$validator = new Validator([
    'even' => fn (mixed $value): bool => is_int($value) && $value % 2 === 0,
    'unique' => new UniqueRule($db),
]);
```

See [Custom Rules](/features/custom-rules).

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
| `$rules` | `array<string, string>` | Rule strings by field: `['email' => 'required\|email']` |

**Returns:** `array<string, list<array{rule: string, params?: list<string>}>>` — errors by field, at most one per field; `[]` when the data is valid.

**Throws:** `InvalidRuleException` — an unknown rule, or parameters a rule cannot use. Raised the first time a rule string is used, whatever the data.

A field with no value — absent, or `null` — is checked by presence rules only, and so is a blank value (`''`, `[]`) of a `nullable` field. The first failing rule ends the field.

**Example:**

```php
$errors = $validator->validate(
    ['email' => 'invalid', 'password' => '123'],
    ['email' => 'required|email', 'password' => 'required|min:8'],
);

// [
//     'email' => [
//         ['rule' => 'email'],
//     ],
//     'password' => [
//         ['rule' => 'min', 'params' => ['8']],
//     ],
// ]
```

`validate()` keeps no state: call it any number of times, from rules too, and share the instance between requests. Each distinct rule string is compiled once per instance; up to 1000 are kept, then the oldest is dropped.

---

## InvalidRuleException

```php
final class InvalidRuleException extends \LogicException
```

A configuration error, not invalid input: fix the rule string or the registration. Custom rules throw it from `checkParams()`, usually as `InvalidRuleException::invalidParams('a table and a column')`; the validator then names the rule as written and its rule string: `Invalid rule 'unique:users' in 'required|unique:users': expects a table and a column.`

---

## Field

```php
final readonly class Field
{
    public string $name;   // the field being validated
    public array $data;    // the whole payload

    public function present(): bool;   // whether the payload has the field's key, null included
}
```

Passed to every rule.

---

## RuleInterface

```php
interface RuleInterface
{
    /**
     * @param list<string> $params
     * @return array{rule: string, params?: list<string>}|null
     */
    public function validate(mixed $value, array $params, Field $field): ?array;
}
```

Return `null` to pass, or the error.

## PresenceRule

```php
interface PresenceRule
{
}
```

Marks a rule that runs for every field: also for one with no value — absent, or `null` — and for a blank value of a `nullable` field.

## ParameterizedRule

```php
interface ParameterizedRule
{
    public function checkParams(array $params): void;
}
```

Checks a rule's parameters once, when a rule string using it is compiled. Throw `InvalidRuleException` to reject them.
