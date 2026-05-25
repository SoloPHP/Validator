<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\BuildsError;

final class MaxRule implements RuleInterface
{
    use BuildsError;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if ($parameter === null) {
            return null;
        }

        // Arrays: maximum element count. Scalars: maximum string length.
        // Without this branch (string)$array triggers "Array to string
        // conversion" and corrupts JSON responses with a PHP warning.
        $size = is_array($value) ? count($value) : mb_strlen((string)$value);

        return $size > (int)$parameter ? $this->buildError('max', $parameter) : null;
    }
}
