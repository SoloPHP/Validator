<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\RuleInterface;
use Solo\Validator\WholeArgumentRule;

/**
 * Reads dates the way Request-Handler reads a date property.
 *
 * date - an int (Unix timestamp), a digit string that is one, or a string PHP
 * parses as a date/time; not an impossible date ('2024-02-31') nor a time of
 * day spelled in digits ('1230'). date:Y-m-d - a string, or an int, written
 * exactly in that format, again without rolling an impossible date over.
 */
final class DateRule implements RuleInterface, WholeArgumentRule
{
    private const ROLLOVER_WARNINGS = ['The parsed date was invalid', 'The parsed time was invalid'];

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        if ($params === []) {
            return self::isDate($value) ? null : ['rule' => 'date'];
        }

        return self::isDateIn($params[0], $value) ? null : ['rule' => 'date_format', 'params' => $params];
    }

    private static function isDate(mixed $value): bool
    {
        if (is_int($value)) {
            return true;
        }
        if (!is_string($value) || $value === '') {
            return false;
        }

        // date_parse() reads the string as `new DateTimeImmutable()` does, with no exception to catch.
        $digits = preg_match('/^-?\d+$/', $value) === 1;
        $parsed = date_parse($value);
        if ($parsed['error_count'] > 0) {
            // The parser rejects a bare Unix timestamp ('1700000000').
            return $digits;
        }

        return !self::rolledOver($parsed['warnings']) && !($digits && $parsed['year'] === false);
    }

    private static function isDateIn(string $format, mixed $value): bool
    {
        if (is_int($value)) {
            $value = (string)$value;
        }
        if (!is_string($value) || $value === '') {
            return false;
        }

        $parsed = \DateTimeImmutable::createFromFormat($format, $value);
        $errors = \DateTimeImmutable::getLastErrors();
        if ($parsed === false || ($errors !== false && self::rolledOver($errors['warnings']))) {
            return false;
        }

        // Written exactly in the format ('2024-1-5' is not Y-m-d), unless the
        // format has parse-only characters (! | ? * # +) formatting cannot repeat.
        return $parsed->format($format) === $value
            || strpbrk(preg_replace('/\\\\./s', '', $format) ?? $format, '!|?*#+') !== false;
    }

    /**
     * @param array<int, string> $warnings
     */
    private static function rolledOver(array $warnings): bool
    {
        return array_intersect($warnings, self::ROLLOVER_WARNINGS) !== [];
    }
}
