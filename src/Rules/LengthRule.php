<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * Exactly N elements of an array, or N characters of a string or number;
 * length:N,bytes - exactly N bytes of a string or number.
 */
final class LengthRule implements RuleInterface, ParameterizedRule
{
    public function checkParams(array $params): void
    {
        Params::expectSize($params);
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        $size = Value::size($value, $params[1] ?? null);

        return $size === (int)$params[0] ? null : ['rule' => 'length', 'params' => $params];
    }
}
