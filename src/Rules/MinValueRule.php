<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * A number (as the numeric rule reads it) of at least N.
 */
final class MinValueRule implements RuleInterface, ParameterizedRule
{
    public function checkParams(array $params): void
    {
        Params::expectNumber($params);
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        $number = Value::toNumber($value);

        return $number !== null && $number >= (float)$params[0] ? null : ['rule' => 'min_value', 'params' => $params];
    }
}
