# Upgrading from 4.1 to 4.2

4.2 reads `null` the way 4.0 reads a missing field: a field with no value — absent, or `null` — is checked by presence rules only. A `null` no longer fails `string`, `email`, `min` or any other rule that checks a value. `''`, `[]` and every other value are validated as before.

| Look for | Do |
|---|---|
| A field that must not be `null` when sent, validated without a presence rule (`'string'`, `'email'`) | Add `filled`, or `required` when it must also be sent |
| A rule of your own that should reject `null` | Implement `PresenceRule`, or pair it with `required` / `filled`: other rules no longer see `null` |
| `nullable` on a field that only had to accept `null` | Nothing: it keeps working, and now matters only for `''` and `[]` |
| Tests expecting a value rule's error for `null` | Expect no error, or the presence rule's error |

---

# Upgrading from 3.x to 4.0

4.0 makes the validator strict about its configuration and consistent in what it accepts: rule strings are compiled once and checked on first use, `validate()` keeps no state, each field reports one error, and the type rules read values the way [solophp/request-handler](https://github.com/SoloPHP/Request-Handler) 4 casts them.

## Checklist

| Look for | Do |
|---|---|
| PHP 8.1 / 8.2 | Upgrade to PHP 8.3+ |
| The `phone` rule | `composer require giggsey/libphonenumber-for-php-lite` — it is optional now |
| `$validator->fails()`, `passed()`, `errors()` | Use what `validate()` returns: `$errors = $validator->validate(...); if ($errors === []) …` |
| `addCustomRule($name, $callback)` | `new Validator([$name => $callback])` — the callback now receives `(mixed $value, array $params, Field $field)` ([Callbacks](#callbacks-and-rule-classes)) |
| `addRule($name, $rule)` | `new Validator([$name => $rule])`, and update `validate()` to the new `RuleInterface` signature ([Rule classes](#callbacks-and-rule-classes)) |
| `use BuildsError` in a rule class | Return the error array directly: `['rule' => 'x', 'params' => $params]` |
| Rule names the validator does not know — typos, unregistered `unique` / `exists` / `same` | They throw `InvalidRuleException` now instead of validating nothing: fix them or register them |
| `min:abc`, `in` without values, an invalid `regex` pattern, `phone:XX` | They throw `InvalidRuleException` now |
| Tests or clients expecting several errors for one field | Expect one: the first failing rule ends the field |
| Optional fields that may be missing, validated without `nullable` | Nothing to do: absent fields are now checked by presence rules only, so `'email'` alone accepts a missing field |
| Tests expecting `['rule' => 'numeric']` from `min_value` / `max_value` | Expect the rule's own error: `['rule' => 'min_value', 'params' => ['18']]` |
| A custom `required_if` / `required_with` / `required_without` | Keep it (a registered rule overrides the built-in one) or drop it for the built-in ([New rules](#new-rules)) |

Request-Handler 4 users only meet the last five rows, and the [stricter rules](#stricter-rules): the handler calls `validate()` and reads its result, so nothing in the handler setup changes.

## Callbacks and rule classes

The parameters arrive split, and the rule gets a `Field` — its own name plus the whole payload — instead of the bare payload:

```php
// 3.x
$validator->addCustomRule('divisible_by', function ($value, ?string $param, array $data) {
    return (int)$value % (int)$param === 0;
});

final class EvenRule implements RuleInterface
{
    public function validate(mixed $value, ?string $parameter, array $data = []): ?array { … }
}
$validator->addRule('even', new EvenRule());

// 4.0
use Solo\Validator\Field;

final class EvenRule implements RuleInterface
{
    public function validate(mixed $value, array $params, Field $field): ?array { … }
}

$validator = new Validator([
    'divisible_by' => fn (mixed $value, array $params): bool => (int)$value % (int)$params[0] === 0,
    'even' => new EvenRule(),
]);
```

`$data[...]` becomes `$field->data[...]`; `$field->name` is the field being validated, so a rule like `confirmed` can find `{$field->name}_confirmation`. Any callable works: `[Rules::class, 'method']` and invokable objects too.

A rule class can implement `ParameterizedRule::checkParams(array $params)` to reject bad parameters with `InvalidRuleException` when the rule string is first used. `PresenceRule` works as before.

## Which rules run

| The field is… | 3.x | 4.0 |
|---|---|---|
| absent | every rule, on `null` | presence rules only |
| blank under `nullable` | presence rules only | presence rules only |
| anything else | every rule | every rule until the first failure |

`filled` now means what its documentation always said — "must not be empty *if present*" — and passes for an absent field.

## Stricter rules

| Rule | 3.x accepted | 4.0 |
|---|---|---|
| `required`, `filled` | `'   '` passes `required` | fails both: whitespace is not a value |
| `integer` | `true` | `['rule' => 'integer']` |
| `numeric` | `'1e400'` (INF) | `['rule' => 'numeric']` |
| `min_value`, `max_value` | INF, `'1e400'` | the rule's error; the parameter must be a finite number |
| `date` | `'2024-02-31'` (rolled over), `'1230'` (a time), `''`, floats | `['rule' => 'date']` |
| `date:FORMAT` | formats with `!` or `\|` never matched | they match; impossible dates fail; an int is read in the format (`20240115` for `Ymd`) |
| `min`, `max`, `length` | a bool read as `'1'` / `''` | fails; `null` still counts as size 0 |
| `in` | `true` matched `in:1` | a bool, `null` or a nested array never matches |
| `regex`, `in`, `length` on arrays / objects | PHP warnings, `"Array"` matched | fail with the rule's error (fixed in 3.3.1) |

`nullable` treats only `null`, `''` and `[]` as blank, as in 3.x: whitespace such as `'   '` is validated by the remaining rules.

## Changed rules

- `length` counts the elements of an array, like `min` and `max`.
- `phone` errors carry the region: `['rule' => 'phone', 'params' => ['US']]`; the region is case-insensitive.
- `date:D, d M Y` and `regex:/^[a,b]+$/` work: a comma in a format or a pattern is kept.

## New rules

`required_if`, `required_unless`, `required_with`, `required_with_all`, `required_without`, `required_without_all` are built in. They are presence rules, so they also run for absent fields and under `nullable` — which is how Request-Handler validates an absent field that has one of them. See [Validation Rules](https://solophp.github.io/Validator/features/rules#required-if).
