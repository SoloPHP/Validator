<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\PresenceRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;

/**
 * required_with:a,b - required when any of the fields is filled;
 * required_with_all - when all of them are; required_without - when any of
 * them is blank or absent; required_without_all - when all of them are.
 */
final class RequiredWithRule implements RuleInterface, PresenceRule, ParameterizedRule
{
    public function __construct(
        private readonly bool $all = false,
        private readonly bool $without = false,
    ) {
    }

    public function checkParams(array $params): void
    {
        Params::expectAtLeast(1, 'at least one field', $params);
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        if (Value::isFilled($value)) {
            return null;
        }

        $filled = 0;
        foreach ($params as $other) {
            if (Value::isFilled($field->data[$other] ?? null)) {
                $filled++;
            }
        }
        // The listed fields that trigger the rule: the filled ones, or with `without` the others.
        $hits = $this->without ? count($params) - $filled : $filled;
        $required = $this->all ? $hits === count($params) : $hits > 0;

        return $required ? ['rule' => $this->name(), 'params' => $params] : null;
    }

    private function name(): string
    {
        return 'required_' . ($this->without ? 'without' : 'with') . ($this->all ? '_all' : '');
    }
}
