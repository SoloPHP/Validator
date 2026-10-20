<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\RuleInterface;

/**
 * An int, a whole float a float holds exactly, or a string PHP reads as an int
 * ('42', '-5'): what Request-Handler casts to an int property. Never a bool.
 */
final class IntegerRule implements RuleInterface
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        $valid = match (true) {
            is_int($value) => true,
            is_float($value) => $value === floor($value) && abs($value) < 9.0E15,
            is_string($value) => filter_var($value, FILTER_VALIDATE_INT) !== false,
            default => false,
        };

        return $valid ? null : ['rule' => 'integer'];
    }
}
