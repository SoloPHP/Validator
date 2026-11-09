<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\InvalidRuleException;
use Solo\Validator\Value;

/**
 * Parameter checks and the error shape shared by the built-in rules.
 *
 * @internal
 */
final class Params
{
    /**
     * A size and an optional unit, as min, max and length take them: `72` or
     * `72,bytes`.
     *
     * @param list<string> $params
     * @throws InvalidRuleException
     */
    public static function expectSize(array $params): void
    {
        $count = count($params);
        if ($count < 1 || $count > 2 || !ctype_digit($params[0]) || ($count === 2 && $params[1] !== 'bytes')) {
            throw InvalidRuleException::invalidParams('one non-negative integer and an optional unit (bytes)');
        }
    }

    /**
     * @param list<string> $params
     * @throws InvalidRuleException
     */
    public static function expectNumber(array $params): void
    {
        if (count($params) !== 1 || Value::toNumber($params[0]) === null) {
            throw InvalidRuleException::invalidParams('one number');
        }
    }

    /**
     * @param list<string> $params
     * @throws InvalidRuleException
     */
    public static function expectAtLeast(int $count, string $expected, array $params): void
    {
        if (count($params) < $count) {
            throw InvalidRuleException::invalidParams($expected);
        }
    }

    /**
     * The error of the rule $rule, its params included when it has any.
     *
     * @param list<string> $params
     * @return array{rule: string, params?: list<string>}
     */
    public static function error(string $rule, array $params): array
    {
        return $params === [] ? ['rule' => $rule] : ['rule' => $rule, 'params' => $params];
    }
}
