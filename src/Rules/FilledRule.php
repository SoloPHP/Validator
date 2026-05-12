<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;

final class FilledRule implements RuleInterface
{
    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if ($value === null) {
            return ['rule' => 'filled'];
        }
        if ((is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
            return ['rule' => 'filled'];
        }
        return null;
    }
}
