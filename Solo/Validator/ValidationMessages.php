<?php

namespace Solo\Validator;

class ValidationMessages
{
    private array $messages;

    public function __construct(array $customMessages = [])
    {
        $this->messages = array_merge($this->getDefaultMessages(), $customMessages);
    }

    private function getDefaultMessages(): array
    {
        return [
            'field' => 'Field {field} does not exist',
            'required' => 'Field {field} is required',
            'unique' => 'Field {field} must be a unique',
            'string' => 'Field {field} must be a string',
            'numeric' => 'Field {field} must be a number',
            'pattern' => 'Field {field} must match the regular expression {pattern}',
            'phone' => 'Field {field} must be a valid phone number',
            'email' => 'Field {field} must be a valid email address',
            'positive' => 'Field {field} must be a positive number',
            'equal' => 'Field {field} must be equal to provided value'
        ];
    }

    public function getMessage(string $type): ?string
    {
        return $this->messages[$type] ?? null;
    }
}