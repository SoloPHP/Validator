<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\RuleInterface;

final class UuidRule implements RuleInterface
{
    private const PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return is_string($value) && preg_match(self::PATTERN, $value) === 1 ? null : ['rule' => 'uuid'];
    }
}
