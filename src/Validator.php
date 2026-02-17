<?php

declare(strict_types=1);

namespace Solo\Validator;

use Solo\Contracts\Validator\ValidatorInterface;

final class Validator implements ValidatorInterface
{
    use ValidationRules;

    /** @var array<string, list<array{rule: string, params?: string[]}>> */
    private array $errors = [];
    /** @var array<string, callable> */
    private array $customRules = [];

    public function validate(array $data, array $rules): array
    {
        $this->errors = [];

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
                        $error = ['rule' => $ruleName];
                        if ($parameter !== null) {
                            $error['params'] = explode(',', $parameter);
                        }
                        $this->errors[$field][] = $error;
                    }
                } else {
                    $error = $this->applyValidation($ruleName, $value, $parameter, $field);
                    if ($error !== null) {
                        $this->errors[$field][] = $error;
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

    /** @return array<string, list<array{rule: string, params?: string[]}>> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function passed(): bool
    {
        return empty($this->errors);
    }

    /**
     * @return ?array{rule: string, params?: string[]}
     */
    private function applyValidation(string $rule, mixed $value, ?string $parameter, string $field): ?array
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
