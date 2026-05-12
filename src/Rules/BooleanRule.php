<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;

final class BooleanRule implements RuleInterface
{
    private const TRUTHY_STRINGS = ['true', 'false', '1', '0', 'yes', 'no', 'on', 'off'];

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if (is_bool($value)) {
            return null;
        }

        if (is_string($value) && in_array(strtolower($value), self::TRUTHY_STRINGS, true)) {
            return null;
        }

        if (is_int($value) && ($value === 0 || $value === 1)) {
            return null;
        }

        return ['rule' => 'boolean'];
    }
}
