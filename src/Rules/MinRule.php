<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\BuildsError;

final class MinRule implements RuleInterface
{
    use BuildsError;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        return ($parameter !== null && mb_strlen((string)$value) < (int)$parameter)
            ? $this->buildError('min', $parameter)
            : null;
    }
}
