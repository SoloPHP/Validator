<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\BuildsError;

final class ArrayRule implements RuleInterface
{
    use BuildsError;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if (!is_array($value)) {
            return ['rule' => 'array'];
        }

        if ($parameter === null) {
            return null;
        }

        $allowedKeys = array_flip(explode(',', $parameter));
        foreach ($value as $key => $_) {
            if (!isset($allowedKeys[(string)$key])) {
                return $this->buildError('array_keys', $parameter);
            }
        }

        return null;
    }
}
