<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\PresenceRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * required_if:field,value[,value...] - required when the other field equals one
 * of the values; required_unless - required unless it does.
 */
final class RequiredIfRule implements RuleInterface, PresenceRule, ParameterizedRule
{
    public function __construct(private readonly bool $unless = false)
    {
    }

    public function checkParams(array $params): void
    {
        Condition::checkParams($params);
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        if (Value::isFilled($value) || Condition::matches($params, $field->data) === $this->unless) {
            return null;
        }

        return ['rule' => $this->unless ? 'required_unless' : 'required_if', 'params' => $params];
    }
}
