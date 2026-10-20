<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\RuleInterface;

final class EmailRule implements RuleInterface
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false ? null : ['rule' => 'email'];
    }
}
