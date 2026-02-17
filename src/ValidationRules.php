<?php

declare(strict_types=1);

namespace Solo\Validator;

use libphonenumber\PhoneNumberUtil;

trait ValidationRules
{
    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateRequired(mixed $value, ?string $parameter, string $field): ?array
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            return ['rule' => 'required'];
        }
        return null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateEmail(mixed $value, ?string $parameter, string $field): ?array
    {
        return !filter_var($value, FILTER_VALIDATE_EMAIL) ? ['rule' => 'email'] : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validatePhone(mixed $value, ?string $regionCode, string $field): ?array
    {
        if (!PhoneNumberUtil::isViablePhoneNumber($value)) {
            return ['rule' => 'phone'];
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        $parsed = $phoneUtil->parse($value, $regionCode);
        if (!$phoneUtil->isValidNumber($parsed)) {
            return ['rule' => 'phone'];
        }

        return null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateLength(mixed $value, ?string $parameter, string $field): ?array
    {
        return (isset($parameter) && strlen((string)$value) !== (int)$parameter)
            ? ['rule' => 'length', 'params' => explode(',', $parameter)]
            : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateMin(mixed $value, ?string $parameter, string $field): ?array
    {
        return (isset($parameter) && strlen((string)$value) < (int)$parameter)
            ? ['rule' => 'min', 'params' => explode(',', $parameter)]
            : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateMax(mixed $value, ?string $parameter, string $field): ?array
    {
        return (isset($parameter) && strlen((string)$value) > (int)$parameter)
            ? ['rule' => 'max', 'params' => explode(',', $parameter)]
            : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateFilled(mixed $value, ?string $parameter, string $field): ?array
    {
        if ($value === null) {
            return ['rule' => 'filled'];
        }
        if ((is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
            return ['rule' => 'filled'];
        }
        return null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateInteger(mixed $value, ?string $parameter, string $field): ?array
    {
        return filter_var($value, FILTER_VALIDATE_INT) === false ? ['rule' => 'integer'] : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateString(mixed $value, ?string $parameter, string $field): ?array
    {
        return !is_string($value) ? ['rule' => 'string'] : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateRegex(mixed $value, ?string $parameter, string $field): ?array
    {
        if (!$parameter || !preg_match('/^\/.*\/[imsxADSUXJu]*$/', $parameter)) {
            return ['rule' => 'regex'];
        }
        return !preg_match($parameter, (string)$value) ? ['rule' => 'regex'] : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateNumeric(mixed $value, ?string $parameter, string $field): ?array
    {
        if (!is_numeric($value)) {
            return ['rule' => 'numeric'];
        }
        return null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateArray(mixed $value, ?string $parameter, string $field): ?array
    {
        if (!is_array($value)) {
            return ['rule' => 'array'];
        }

        if ($parameter !== null) {
            $allowedKeys = explode(',', $parameter);

            foreach (array_keys($value) as $key) {
                if (!in_array((string) $key, $allowedKeys, true)) {
                    return ['rule' => 'array_keys', 'params' => explode(',', $parameter)];
                }
            }
        }

        return null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateMinValue(mixed $value, ?string $parameter, string $field): ?array
    {
        if (!is_numeric($value)) {
            return ['rule' => 'numeric'];
        }

        $min = (float)$parameter;
        return ($value < $min)
            ? ['rule' => 'min_value', 'params' => explode(',', (string)$parameter)]
            : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateMaxValue(mixed $value, ?string $parameter, string $field): ?array
    {
        if (!is_numeric($value)) {
            return ['rule' => 'numeric'];
        }

        $max = (float)$parameter;
        return ($value > $max)
            ? ['rule' => 'max_value', 'params' => explode(',', (string)$parameter)]
            : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateIn(mixed $value, ?string $parameter, string $field): ?array
    {
        if (!$parameter) {
            return ['rule' => 'in'];
        }

        $allowedValues = explode(',', $parameter);

        if (is_array($value)) {
            foreach ($value as $item) {
                if (!in_array((string)$item, $allowedValues, true)) {
                    return ['rule' => 'in', 'params' => explode(',', $parameter)];
                }
            }
            return null;
        }

        return !in_array((string)$value, $allowedValues, true)
            ? ['rule' => 'in', 'params' => explode(',', $parameter)]
            : null;
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateBoolean(mixed $value, ?string $parameter, string $field): ?array
    {
        if (is_bool($value)) {
            return null;
        }

        // Accept common boolean representations
        if (is_string($value)) {
            $lower = strtolower($value);
            if (in_array($lower, ['true', 'false', '1', '0', 'yes', 'no', 'on', 'off'], true)) {
                return null;
            }
        }

        if (is_int($value) && in_array($value, [0, 1], true)) {
            return null;
        }

        return ['rule' => 'boolean'];
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateDate(mixed $value, ?string $parameter, string $field): ?array
    {
        if (!is_string($value) && !is_numeric($value)) {
            return ['rule' => 'date'];
        }

        if ($parameter !== null) {
            $parsed = \DateTime::createFromFormat($parameter, (string) $value);

            return ($parsed !== false && $parsed->format($parameter) === (string) $value)
                ? null
                : ['rule' => 'date_format', 'params' => explode(',', $parameter)];
        }

        try {
            $dateString = is_numeric($value) ? '@' . $value : (string) $value;
            new \DateTime($dateString);
            return null;
        } catch (\Exception) {
            return ['rule' => 'date'];
        }
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function validateUuid(mixed $value, ?string $parameter, string $field): ?array
    {
        if (!is_string($value)) {
            return ['rule' => 'uuid'];
        }

        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)
            ? null
            : ['rule' => 'uuid'];
    }
}
