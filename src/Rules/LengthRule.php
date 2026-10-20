<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\BuildsError;
use Solo\Validator\CastsToString;

final class LengthRule implements RuleInterface
{
    use BuildsError;
    use CastsToString;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if ($parameter === null) {
            return null;
        }

        $string = $this->castToString($value);

        return ($string === null || mb_strlen($string) !== (int)$parameter)
            ? $this->buildError('length', $parameter)
            : null;
    }
}
