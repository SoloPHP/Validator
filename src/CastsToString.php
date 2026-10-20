<?php

declare(strict_types=1);

namespace Solo\Validator;

trait CastsToString
{
    /**
     * The string a string-based rule checks; null where the cast would warn
     * or throw: an array or an object without __toString().
     */
    private function castToString(mixed $value): ?string
    {
        if (is_array($value) || (is_object($value) && !$value instanceof \Stringable)) {
            return null;
        }
        return (string)$value;
    }
}
