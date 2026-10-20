<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * true / false, 1 / 0, or 'true', '1', 'yes', 'on' / 'false', '0', 'no', 'off'
 * in any case: what Request-Handler casts to a bool property.
 */
final class BooleanRule implements RuleInterface
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return Value::toBool($value) === null ? ['rule' => 'boolean'] : null;
    }
}
