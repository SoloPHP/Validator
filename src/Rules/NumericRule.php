<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * A finite int, float or numeric string: what Request-Handler casts to a float
 * property. Never a bool, nor '1e400', which overflows to INF.
 */
final class NumericRule implements RuleInterface
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return Value::toNumber($value) === null ? ['rule' => 'numeric'] : null;
    }
}
