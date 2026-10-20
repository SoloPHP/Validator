<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * Exactly N elements of an array, or N characters of a string or number.
 */
final class LengthRule implements RuleInterface, ParameterizedRule
{
    public function checkParams(array $params): void
    {
        Params::expectInteger($params);
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return Value::size($value) === (int)$params[0] ? null : ['rule' => 'length', 'params' => $params];
    }
}
