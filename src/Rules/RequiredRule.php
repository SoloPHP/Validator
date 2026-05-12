<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;

final class RequiredRule implements RuleInterface
{
    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            return ['rule' => 'required'];
        }
        return null;
    }
}
