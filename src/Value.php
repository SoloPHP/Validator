<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * Readings of a value shared by the validator and the built-in rules, the
 * typed ones matching how Request-Handler reads a property of that type.
 *
 * @internal
 */
final class Value
{
    /**
     * Null, '' or []: what `nullable` lets through.
     */
    public static function isBlank(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

    /**
     * Neither blank nor a string of only whitespace: what presence rules demand.
     */
    public static function isFilled(mixed $value): bool
    {
        return is_string($value) ? trim($value) !== '' : $value !== null && $value !== [];
    }

    /**
     * A string, an int, a finite float or a Stringable as a string. Null for
     * anything else: bool, null, arrays, other objects.
     */
    public static function toString(mixed $value): ?string
    {
        return match (true) {
            is_string($value) => $value,
            is_int($value), $value instanceof \Stringable => (string)$value,
            is_float($value) => is_finite($value) ? (string)$value : null,
            default => null,
        };
    }

    /**
     * A finite int, float or numeric string as a float. Null for anything
     * else: bools, INF and NAN, '1e400' (which overflows to INF).
     */
    public static function toNumber(mixed $value): ?float
    {
        if (!is_numeric($value)) {
            return null;
        }
        $number = (float)$value;

        return is_finite($number) ? $number : null;
    }

    /**
     * true / false, 1 / 0, or the strings 'true', '1', 'yes', 'on' /
     * 'false', '0', 'no', 'off' in any case. Null for anything else.
     */
    public static function toBool(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if ($value === 0 || $value === 1) {
            return $value === 1;
        }
        if (!is_string($value)) {
            return null;
        }

        return match (strtolower($value)) {
            'true', '1', 'yes', 'on' => true,
            'false', '0', 'no', 'off' => false,
            default => null,
        };
    }

    /**
     * Character count of anything with a string form, element count of an
     * array, 0 for null. Null for a bool or an object without __toString().
     */
    public static function size(mixed $value): ?int
    {
        if (is_string($value)) {
            return mb_strlen($value);
        }
        if (is_array($value)) {
            return count($value);
        }
        if ($value === null) {
            return 0;
        }
        $string = self::toString($value);
        return $string === null ? null : mb_strlen($string);
    }
}
