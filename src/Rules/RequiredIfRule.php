<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\PresenceRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * required_if:field,value[,value...] - required when the other field equals one
 * of the values; required_unless - required unless it does.
 */
final class RequiredIfRule implements RuleInterface, PresenceRule, ParameterizedRule
{
    public function __construct(private readonly bool $unless = false)
    {
    }

    public function checkParams(array $params): void
    {
        Params::expectAtLeast(2, 'a field and at least one value', $params);
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        if (Value::isFilled($value)) {
            return null;
        }

        $other = $field->data[$params[0]] ?? null;
        $matches = array_filter(
            array_slice($params, 1),
            static fn (string $expected): bool => self::equals($other, $expected),
        ) !== [];
        $required = $this->unless ? !$matches : $matches;

        return $required ? ['rule' => $this->unless ? 'required_unless' : 'required_if', 'params' => $params] : null;
    }

    /**
     * Compare the way a rule string spells a value: `true` / `false` match what
     * the boolean rule reads as such (true, 1, 'on', 'yes'...), `null` matches
     * null or an absent field, `1` / `0` also match true / false.
     */
    private static function equals(mixed $actual, string $expected): bool
    {
        return match (strtolower($expected)) {
            'true' => Value::toBool($actual) === true,
            'false' => Value::toBool($actual) === false,
            'null' => $actual === null,
            default => Value::toString(is_bool($actual) ? (int)$actual : $actual) === $expected,
        };
    }
}
