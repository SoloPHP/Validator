<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * prohibited_if:field,value[,value...] - must not be filled when the other
 * field equals one of the values; prohibited_unless - unless it does. The
 * condition reads the other field the way required_if does.
 *
 * Not a presence rule: an absent or blank field passes it anyway.
 */
final class ProhibitedIfRule implements RuleInterface, ParameterizedRule
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
        if (!Value::isFilled($value) || Condition::matches($params, $field->data) === $this->unless) {
            return null;
        }

        return ['rule' => $this->unless ? 'prohibited_unless' : 'prohibited_if', 'params' => $params];
    }
}
