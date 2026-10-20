<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * At least N elements of an array, or N characters of a string or number.
 */
final class MinRule implements RuleInterface, ParameterizedRule
{
    public function checkParams(array $params): void
    {
        Params::expectInteger($params);
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        $size = Value::size($value);

        return $size !== null && $size >= (int)$params[0] ? null : ['rule' => 'min', 'params' => $params];
    }
}
