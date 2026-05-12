<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;

final class UuidRule implements RuleInterface
{
    private const PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if (!is_string($value)) {
            return ['rule' => 'uuid'];
        }

        return preg_match(self::PATTERN, $value) ? null : ['rule' => 'uuid'];
    }
}
