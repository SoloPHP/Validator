<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\RuleInterface;

final class ArrayRule implements RuleInterface
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        if (!is_array($value)) {
            return ['rule' => 'array'];
        }

        if ($params !== [] && array_diff_key($value, array_flip($params)) !== []) {
            return ['rule' => 'array_keys', 'params' => $params];
        }

        return null;
    }
}
