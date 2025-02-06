<?php declare(strict_types=1);

namespace Solo;

use Solo\Validator\ValidatorInterface;

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

            foreach ($rulesArray as $rule) {
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $parameter] = explode(':', $rule, 2);
                } else {
                    $ruleName = $rule;
                    $parameter = null;
                }

                if (isset($this->customRules[$ruleName])) {
                    $isValid = call_user_func($this->customRules[$ruleName], $data[$field] ?? null, $parameter, $data);
                    if (!$isValid) {
                        $this->addError($field, $this->getErrorMessage($field, $ruleName, $messages));
                    }
                } else {
                    $message = $this->applyValidation($ruleName, $data[$field] ?? null, $parameter, $field);
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

    private function getErrorMessage(string $field, string $rule, array $messages, string $default = ''): string
    {
        $customKey = "{$field}.{$rule}";
        return $messages[$customKey] ?? $messages[$rule] ?? $default ?: sprintf('The %s field failed the %s validation.', $field, $rule);
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function applyValidation(string $rule, mixed $value, ?string $parameter, string $field): ?string
    {
        $method = 'validate' . ucfirst($rule);
        if (method_exists($this, $method)) {
            return $this->$method($value, $parameter, $field);
        }
        return null;
    }
}