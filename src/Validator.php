<?php

declare(strict_types=1);

namespace Solo\Validator;

use Solo\Validator\ValidatorInterface;
use Solo\Validator\ValidationRules;

final class Validator implements ValidatorInterface
{
    use ValidationRules;

    private array $errors = [];
    private array $customRules = [];
    private array $messages = [];

    public function __construct(array $messages = [])
    {
        $this->messages = array_merge($this->defaultMessages, $messages);
    }

    public function validate(array $data, array $rules, array $customMessages = []): array
    {
        $this->errors = [];
        $messages = array_merge($this->messages, $customMessages);

        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            $value = $data[$field] ?? null;

            if ($value === null && in_array('nullable', $rulesArray, true)) {
                continue;
            }

            foreach ($rulesArray as $rule) {
                [$ruleName, $parameter] = $this->parseRule($rule);

                if (isset($this->customRules[$ruleName])) {
                    $isValid = call_user_func($this->customRules[$ruleName], $value, $parameter, $data);
                    if (!$isValid) {
                        $this->addError($field, $this->getErrorMessage($field, $ruleName, $messages));
                    }
                } else {
                    $message = $this->applyValidation($ruleName, $value, $parameter, $field);
                    if ($message) {
                        $this->addError($field, $this->getErrorMessage($field, $ruleName, $messages, $message));
                    }
                }
            }
        }

        return $this->errors;
    }

    public function addCustomRule(string $name, callable $callback): void
    {
        $this->customRules[$name] = $callback;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function passed(): bool
    {
        return empty($this->errors);
    }

    private function getErrorMessage(
        string $field,
        string $rule,
        array $messages,
        string $default = ''
    ): string {
        return $messages["{$field}.{$rule}"]
            ?? $messages[$rule]
            ?? $default
            ?? sprintf('The %s field failed the %s validation.', $field, $rule);
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function applyValidation(string $rule, mixed $value, ?string $parameter, string $field): ?string
    {
        $method = $this->getValidationMethodName($rule);
        if (method_exists($this, $method)) {
            return $this->$method($value, $parameter, $field);
        }
        return null;
    }

    private function getValidationMethodName(string $rule): string
    {
        $methodName = str_replace('_', ' ', $rule);
        $methodName = ucwords($methodName);
        $methodName = str_replace(' ', '', $methodName);
        return 'validate' . $methodName;
    }

    private function parseRule(string $rule): array
    {
        if (str_contains($rule, ':')) {
            [$ruleName, $parameter] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        return [$ruleName, $parameter];
    }
}
