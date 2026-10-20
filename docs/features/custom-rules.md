# Custom Rules

Extend validation with your own rules. Register them in the constructor, by name:

```php
use Solo\Validator\Validator;

$validator = new Validator([
    'even' => fn (mixed $value): bool => is_int($value) && $value % 2 === 0,
    'unique' => new UniqueRule($connection),
]);
```

A rule is a **callable** for simple checks, or a class implementing **`RuleInterface`** for dependencies, its own error shape or presence semantics. Registering a built-in name (`'email' => …`) overrides the built-in rule.

The rules of an instance are fixed once it is constructed. Names that no rule string could refer to are rejected with an `InvalidRuleException`: `nullable` is reserved, and a name cannot be empty, contain `|` or `:`, or have surrounding whitespace.

---

## Callable Rules

Any callable works — a closure, `[Class::class, 'method']`, an invokable object:

```php
function (mixed $value, array $params, Field $field): bool
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | `mixed` | The field value being validated |
| `$params` | `list<string>` | The rule's parameters: `'divisible_by:5'` gives `['5']` |
| `$field` | `Field` | The field's name and the whole payload |

Returning `false` fails the field with `['rule' => name]`, plus `'params' => $params` when the rule has parameters. Callables may declare fewer parameters than they receive, but PHP's own functions reject extra arguments: wrap them, `'alpha' => fn ($value) => is_string($value) && ctype_alpha($value)`.

### Even Number

```php
$validator = new Validator([
    'even' => fn (mixed $value): bool => is_int($value) && $value % 2 === 0,
]);

$validator->validate(['number' => 3], ['number' => 'even']);
// ['number' => [['rule' => 'even']]]
```

### With Parameters

```php
$validator = new Validator([
    'divisible_by' => fn (mixed $value, array $params): bool
        => is_int($value) && $value % (int)$params[0] === 0,
]);

$validator->validate(['quantity' => 17], ['quantity' => 'divisible_by:5']);
// ['quantity' => [['rule' => 'divisible_by', 'params' => ['5']]]]
```

### Using the Field and the Payload

`Field` carries the field's `name`, the whole payload as `data`, and `present()` — whether the payload has the key at all:

```php
use Solo\Validator\Field;

$validator = new Validator([
    'confirmed' => fn (mixed $value, array $params, Field $field): bool
        => $value === ($field->data[$params[0] ?? $field->name . '_confirmation'] ?? null),
    'different' => fn (mixed $value, array $params, Field $field): bool
        => $value !== ($field->data[$params[0]] ?? null),
]);

$rules = [
    'password' => 'required|min:8|confirmed',
    'new_password' => 'required|different:old_password',
];
```

### A Pattern with Alternation

A `regex:` parameter cannot contain `|`, which separates rules. Name the pattern instead:

```php
$validator = new Validator([
    'size' => fn (mixed $value): bool => is_string($value) && preg_match('/^(S|M|L|XL)$/', $value) === 1,
]);
```

### Static Methods

```php
final class Rules
{
    public static function slug(mixed $value): bool
    {
        return is_string($value) && preg_match('/^[a-z0-9-]+$/', $value) === 1;
    }
}

$validator = new Validator(['slug' => [Rules::class, 'slug']]);
```

---

## Class-Based Rules (RuleInterface)

Implement `Solo\Validator\RuleInterface` and return the full error, or `null` to pass:

```php
use Solo\Validator\Field;
use Solo\Validator\RuleInterface;

final class EvenRule implements RuleInterface
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return is_int($value) && $value % 2 === 0 ? null : ['rule' => 'even'];
    }
}

$validator = new Validator(['even' => new EvenRule()]);
```

### With Constructor Dependencies

```php
use Doctrine\DBAL\Connection;
use Solo\Validator\Field;
use Solo\Validator\InvalidRuleException;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\RuleInterface;

final class UniqueRule implements RuleInterface, ParameterizedRule
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function checkParams(array $params): void
    {
        if (count($params) !== 2) {
            throw InvalidRuleException::invalidParams('a table and a column');
        }
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        [$table, $column] = $params;

        $exists = (bool) $this->db->createQueryBuilder()
            ->select('1')
            ->from($this->db->quoteIdentifier($table))
            ->where($this->db->quoteIdentifier($column) . ' = :value')
            ->setParameter('value', $value)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();

        // The error omits params: table and column names are internal schema.
        return $exists ? ['rule' => 'unique'] : null;
    }
}

$validator = new Validator(['unique' => new UniqueRule($db)]);

$rules = ['email' => 'required|email|unique:users,email'];
```

### Custom Error Variants

The rule chooses what to return — useful when one rule has distinct failure modes:

```php
final class PasswordStrengthRule implements RuleInterface
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        if (!is_string($value) || strlen($value) < 8) {
            return ['rule' => 'password_strength', 'params' => ['too_short']];
        }
        if (!preg_match('/[A-Z]/', $value) || !preg_match('/[0-9]/', $value)) {
            return ['rule' => 'password_strength', 'params' => ['weak']];
        }
        return null;
    }
}
```

### Checking Parameters (ParameterizedRule)

A rule implementing `Solo\Validator\ParameterizedRule` gets its parameters once, when a rule string using it is first compiled — whatever the input, and even when an earlier rule fails the field. Throw `InvalidRuleException` for parameters it cannot use, as `UniqueRule` above does — `InvalidRuleException::invalidParams()` takes what the rule expects, and the validator adds the rule as written and its rule string: `Invalid rule 'unique:users' in 'required|unique:users': expects a table and a column.` A misconfigured rule string then fails in development instead of behaving oddly in production.

### Presence Rules (PresenceRule)

For an absent field, and for a blank value of a `nullable` field, only presence rules run. A rule opts in by implementing the marker interface `Solo\Validator\PresenceRule`; the built-in `required`, `filled` and `required_*` rules do. Callables are never presence rules.

```php
use Solo\Validator\Field;
use Solo\Validator\PresenceRule;
use Solo\Validator\RuleInterface;

final class RequiredForAdminsRule implements RuleInterface, PresenceRule
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        $required = ($field->data['role'] ?? null) === 'admin';

        return $required && ($value === null || $value === '') ? ['rule' => 'required_for_admins'] : null;
    }
}

$validator = new Validator(['required_for_admins' => new RequiredForAdminsRule()]);

$rules = ['token' => 'nullable|required_for_admins'];
```

### Callable vs RuleInterface

| | Callable | `RuleInterface` |
|---|---|---|
| Returns | `bool` | the error array, or `null` |
| Error shape | `['rule' => name, 'params' => $params]` | whatever the rule returns |
| Parameter checks | — | `ParameterizedRule` |
| Presence semantics | — | `PresenceRule` |
| Best for | Quick checks | Dependencies, custom errors |

---

## Reusable Validator Setup

Build the validator once — in a factory or your container — and share it: it keeps no state between calls.

```php
final class ValidatorFactory
{
    public static function create(Connection $db): Validator
    {
        return new Validator([
            'unique' => new UniqueRule($db),
            'slug' => [Rules::class, 'slug'],
            'confirmed' => fn (mixed $value, array $params, Field $field): bool
                => $value === ($field->data[$field->name . '_confirmation'] ?? null),
        ]);
    }
}
```
