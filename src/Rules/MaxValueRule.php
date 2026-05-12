<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\BuildsError;

final class MaxValueRule implements RuleInterface
{
    use BuildsError;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if (!is_numeric($value)) {
            return ['rule' => 'numeric'];
        }

        return ($value > (float)$parameter)
            ? $this->buildError('max_value', $parameter)
            : null;
    }
}
