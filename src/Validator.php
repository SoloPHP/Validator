<?php

declare(strict_types=1);

namespace Solo\Validator;

use Solo\Contracts\Validator\ValidatorInterface;

final class Validator implements ValidatorInterface
{
    use ValidationRules;

    /** @var array<string, list<string>> */
    private array $errors = [];
    /** @var array<string, callable> */
    private array $customRules = [];
    /** @var array<string, string> */
    private array $messages = [];

    /** @param array<string, string> $messages */
    public function __construct(array $messages = [])
    {
        $this->messages = array_merge($this->defaultMessages, $messages);
    }

    public function validate(array $data, array $rules, array $messages = []): array
    {
        $this->errors = [];
        $messages = array_merge($this->messages, $messages);

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
                        $this->addError($field, $this->getErrorMessage($field, $ruleName, $messages, '', $parameter));
                    }
                } else {
                    $message = $this->applyValidation($ruleName, $value, $parameter, $field);
                    if ($message) {
                        $errorMessage = $this->getErrorMessage(
                            $field,
                            $ruleName,
                            $messages,
                            $message,
                            $parameter
                        );
                        $this->addError($field, $errorMessage);
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

    /** @return array<string, list<string>> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function passed(): bool
    {
        return empty($this->errors);
    }

    /**
     * @param array<string, string> $messages
     */
    private function getErrorMessage(
        string $field,
        string $rule,
        array $messages,
        string $default = '',
        ?string $parameter = null
    ): string {
        $message = $messages["{$field}.{$rule}"]
            ?? $messages[$rule]
            ?? $default
            ?: sprintf('The %s field failed the %s validation.', $field, $rule);

        return str_replace([':field', ':param'], [$field, $parameter ?? ''], $message);
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

    /**
     * @return array{0: string, 1: string|null}
     */
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
