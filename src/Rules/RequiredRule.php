<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\PresenceRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

final class RequiredRule implements RuleInterface, PresenceRule
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return Value::isFilled($value) ? null : ['rule' => 'required'];
    }
}
