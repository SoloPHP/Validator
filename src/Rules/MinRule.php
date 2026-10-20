<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\BuildsError;
use Solo\Validator\CastsToString;

final class MinRule implements RuleInterface
{
    use BuildsError;
    use CastsToString;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if ($parameter === null) {
            return null;
        }

        // Arrays: minimum element count. Scalars: minimum string length.
        // Without this branch (string)$array triggers "Array to string
        // conversion" and corrupts JSON responses with a PHP warning.
        if (is_array($value)) {
            $size = count($value);
        } else {
            $string = $this->castToString($value);
            if ($string === null) {
                return $this->buildError('min', $parameter);
            }
            $size = mb_strlen($string);
        }

        return $size < (int)$parameter ? $this->buildError('min', $parameter) : null;
    }
}
