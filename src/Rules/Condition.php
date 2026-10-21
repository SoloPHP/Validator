<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\InvalidRuleException;
use Solo\Validator\Value;

/**
 * The `field,value[,value...]` condition of required_if / prohibited_if and
 * their `unless` forms: whether another field equals one of the values.
 *
 * @internal
 */
final class Condition
{
    /**
     * @param list<string> $params
     * @throws InvalidRuleException
     */
    public static function checkParams(array $params): void
    {
        Params::expectAtLeast(2, 'a field and at least one value', $params);
    }

    /**
     * @param list<string> $params The other field, then the values
     * @param array<string, mixed> $data The whole payload
     */
    public static function matches(array $params, array $data): bool
    {
        $other = $data[$params[0]] ?? null;
        foreach (array_slice($params, 1) as $expected) {
            if (self::equals($other, $expected)) {
                return true;
            }
        }

        return false;
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
