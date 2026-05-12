<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;

final class IntegerRule implements RuleInterface
{
    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        return filter_var($value, FILTER_VALIDATE_INT) === false ? ['rule' => 'integer'] : null;
    }
}
