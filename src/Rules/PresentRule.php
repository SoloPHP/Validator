<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\PresenceRule;
use Solo\Validator\RuleInterface;

/**
 * present - the field must be in the payload; any value it carries passes,
 * null, '' and [] included. `required` is the one that also wants it filled.
 */
final class PresentRule implements RuleInterface, PresenceRule
{
    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return $field->present() ? null : ['rule' => 'present'];
    }
}
