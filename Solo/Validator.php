<?php declare(strict_types=1);

namespace Solo;

use Solo\Validator\MessagesTrait;
use Solo\Validator\RulesTrait;

class Validator
{
    use MessagesTrait;
    use RulesTrait;

    private array $errors = [];
    private array $fields;
    private string $field;
    private $value = '';

    public function __construct(array $customMessages = [])
    {
        $this->messages = $customMessages ?? $this->messages;
    }

    public function collect(array $fields): void
    {
        $this->fields = $fields;
    }

    public function validate(string $field): self
    {
        $this->field = $field;

        if (array_key_exists($field, $this->fields)) {
            $this->value = is_null($this->fields[$field]) ? '' : $this->fields[$field];
        } else {
            $this->value = '';
            $this->addError('field');
        }

        return $this;
    }

    private function addError(string $type, array $placeholders = []): void
    {
        if (isset($this->errors[$this->field])) {
            return;
        }

        if (isset($this->messages[$type])) {
            $placeholders['{field}'] = $this->field;
            $this->errors[$this->field] = str_replace(array_keys($placeholders), array_values($placeholders), $this->messages[$type]);
        } else {
            $this->errors[$this->field] = sprintf('Some error in field: %s', $this->field);
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function failed(): bool
    {
        return !empty($this->errors);
    }
}