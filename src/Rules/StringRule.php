<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\RuleInterface;

final class StringRule implements RuleInterface
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return is_string($value) ? null : ['rule' => 'string'];
    }
}
