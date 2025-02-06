<?php

namespace Solo\Validator;

trait ValidationRules
{
    private array $defaultMessages = [
        'required' => 'The :field field is required.',
        'email' => 'The :field must be a valid email address.',
        'min' => 'The :field must be at least :param characters.',
        'max' => 'The :field must not exceed :param characters.',
    ];

    private function validateRequired(mixed $value, ?string $parameter, string $field): ?string
    {
        return empty($value) ? $this->formatMessage('required', $field) : null;
    }

    private function validateEmail(mixed $value, ?string $parameter, string $field): ?string
    {
        return !filter_var($value, FILTER_VALIDATE_EMAIL) ? $this->formatMessage('email', $field) : null;
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

    private function formatMessage(string $key, string $field, ?string $param = null): string
    {
        $message = $this->defaultMessages[$key] ?? 'Validation error.';
        return str_replace([':field', ':param'], [$field, $param ?? ''], $message);
    }
}