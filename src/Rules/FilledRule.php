<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\PresenceRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * May be absent; when present, must not be blank.
 */
final class FilledRule implements RuleInterface, PresenceRule
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return !Value::isFilled($value) && $field->present() ? ['rule' => 'filled'] : null;
    }
}
