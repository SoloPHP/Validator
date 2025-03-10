<?php

namespace Solo\Validator;

use libphonenumber\PhoneNumberUtil;

trait ValidationRules
{
    protected array $defaultMessages = [
        'required' => 'The :field field is required.',
        'email' => 'The :field must be a valid email address.',
        'phone' => 'The :field must be a valid phone number.',
        'min' => 'The :field must be at least :param.',
        'max' => 'The :field must not exceed :param.',
        'filled' => 'The :field must not be empty.',
        'integer' => 'The :field must be an integer.',
        'string' => 'The :field must be a string.',
        'regex' => 'The :field format is invalid.',
        'numeric' => 'The :field must be a number.',
        'array' => 'The :field must be an array.',
        'min_value' => 'The :field must be at least :param.',
        'max_value' => 'The :field must not exceed :param.'
    ];

    private function validateRequired(mixed $value, ?string $parameter, string $field): ?string
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            return $this->formatMessage('required', $field);
        }
        return null;
    }

    private function validateEmail(mixed $value, ?string $parameter, string $field): ?string
    {
        return !filter_var($value, FILTER_VALIDATE_EMAIL) ? $this->formatMessage('email', $field) : null;
    }

    private function validatePhone(mixed $value, ?string $regionCode, string $field): ?string
    {
        if (!PhoneNumberUtil::isViablePhoneNumber($value)) {
            return $this->formatMessage('phone', $field);
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        $parsed = $phoneUtil->parse($value, $regionCode);
        if (!$phoneUtil->isValidNumber($parsed)) {
            return $this->formatMessage('phone', $field);
        }

        return null;
    }

    private function validateMin(mixed $value, ?string $parameter, string $field): ?string
    {
        return (isset($parameter) && strlen((string)$value) < (int)$parameter)
            ? $this->formatMessage('min', $field, $parameter)
            : null;
    }

    private function validateMax(mixed $value, ?string $parameter, string $field): ?string
    {
        return (isset($parameter) && strlen((string)$value) > (int)$parameter)
            ? $this->formatMessage('max', $field, $parameter)
            : null;
    }

    private function validateFilled(mixed $value, ?string $parameter, string $field): ?string
    {
        if ($value === null) {
            return $this->formatMessage('filled', $field);
        }
        if ((is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
            return $this->formatMessage('filled', $field);
        }
        return null;
    }

    private function validateInteger(mixed $value, ?string $parameter, string $field): ?string
    {
        return filter_var($value, FILTER_VALIDATE_INT) === false ? $this->formatMessage('integer', $field) : null;
    }

    private function validateString(mixed $value, ?string $parameter, string $field): ?string
    {
        return !is_string($value) ? $this->formatMessage('string', $field) : null;
    }

    private function validateRegex(mixed $value, ?string $parameter, string $field): ?string
    {
        if (!$parameter || !preg_match('/^\/.*\/[imsxADSUXJu]*$/', $parameter)) {
            return $this->formatMessage('regex', $field);
        }
        return !preg_match($parameter, (string)$value) ? $this->formatMessage('regex', $field) : null;
    }

    private function formatMessage(string $key, string $field, ?string $param = null): string
    {
        $message = $this->defaultMessages[$key] ?? 'Validation error.';
        return str_replace([':field', ':param'], [$field, $param ?? ''], $message);
    }

    private function validateNumeric(mixed $value, ?string $parameter, string $field): ?string
    {
        if (!is_numeric($value)) {
            return $this->formatMessage('numeric', $field);
        }
        return null;
    }

    private function validateArray(mixed $value, ?string $parameter, string $field): ?string
    {
        if (!is_array($value)) {
            return $this->formatMessage('array', $field);
        }
        return null;
    }

    private function validateMinValue(mixed $value, ?string $parameter, string $field): ?string
    {
        if (!is_numeric($value)) {
            return $this->formatMessage('numeric', $field);
        }

        $min = (float)$parameter;
        return ($value < $min)
            ? $this->formatMessage('min_value', $field, $parameter)
            : null;
    }

    private function validateMaxValue(mixed $value, ?string $parameter, string $field): ?string
    {
        if (!is_numeric($value)) {
            return $this->formatMessage('numeric', $field);
        }

        $max = (float)$parameter;
        return ($value > $max)
            ? $this->formatMessage('max_value', $field, $parameter)
            : null;
    }
}
