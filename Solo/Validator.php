<?php declare(strict_types=1);

namespace Solo;

use Solo\Validator\Interfaces\RulesInterface;
use Solo\Validator\Interfaces\ValidatorInterface;
use Solo\Validator\ValidationMessages;
use Solo\Validator\RulesTrait;

class Validator implements ValidatorInterface, RulesInterface
{
    use RulesTrait;

    private array $errors = [];
    private string $field;
    private $value = '';
    private ValidationMessages $messages;

    public function __construct(array $customMessages = [])
    {
        $this->messages = new ValidationMessages($customMessages);
    }

    public function validate(string $field, $value): self
    {
        $this->field = $field;
        $this->value = is_null($value) ? '' : $value;
        return $this;
    }

    private function addError(string $type, ?string $message = null, array $placeholders = []): void
    {
        if (isset($this->errors[$this->field])) {
            return;
        }

        if ($message !== null) {
            $this->errors[$this->field] = $message;
        } elseif ($defaultMessage = $this->messages->getMessage($type)) {
            $placeholders['{field}'] = $this->field;
            $this->errors[$this->field] = str_replace(
                array_keys($placeholders),
                array_values($placeholders),
                $defaultMessage
            );
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