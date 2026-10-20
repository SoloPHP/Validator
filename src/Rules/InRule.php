<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * The value, or every element of an array value, is one of the listed values.
 */
final class InRule implements RuleInterface, ParameterizedRule
{
    public function checkParams(array $params): void
    {
        Params::expectAtLeast(1, 'at least one value', $params);
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        foreach (is_array($value) ? $value : [$value] as $item) {
            $string = Value::toString($item);
            if ($string === null || !in_array($string, $params, true)) {
                return ['rule' => 'in', 'params' => $params];
            }
        }

        return null;
    }
}
