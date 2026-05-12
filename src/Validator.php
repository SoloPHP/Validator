<?php

declare(strict_types=1);

namespace Solo\Validator;

use Solo\Contracts\Validator\ValidatorInterface;
use Solo\Validator\BuildsError;

/**
 * @phpstan-import-type ValidationError from RuleInterface
 */
final class Validator implements ValidatorInterface
{
    use BuildsError;

    /** @var array<string, RuleInterface>|null */
    private static ?array $defaults = null;

    /** @var array<string, RuleInterface|callable> */
    private array $rules;

    /** @var array<string, list<ValidationError>> */
    private array $errors = [];

    public function __construct()
    {
        self::$defaults ??= [
            'required'  => new Rules\RequiredRule(),
            'email'     => new Rules\EmailRule(),
            'phone'     => new Rules\PhoneRule(),
            'length'    => new Rules\LengthRule(),
            'min'       => new Rules\MinRule(),
            'max'       => new Rules\MaxRule(),
            'filled'    => new Rules\FilledRule(),
            'integer'   => new Rules\IntegerRule(),
            'string'    => new Rules\StringRule(),
            'regex'     => new Rules\RegexRule(),
            'numeric'   => new Rules\NumericRule(),
            'array'     => new Rules\ArrayRule(),
            'min_value' => new Rules\MinValueRule(),
            'max_value' => new Rules\MaxValueRule(),
            'in'        => new Rules\InRule(),
            'boolean'   => new Rules\BooleanRule(),
            'date'      => new Rules\DateRule(),
            'uuid'      => new Rules\UuidRule(),
        ];
        $this->rules = self::$defaults;
    }

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
                $handler = $this->rules[$ruleName] ?? null;

                if ($handler === null) {
                    continue;
                }

                if ($handler instanceof RuleInterface) {
                    $error = $handler->validate($value, $parameter, $data);
                } else {
                    $error = $handler($value, $parameter, $data)
                        ? null
                        : $this->buildError($ruleName, $parameter);
                }

                if ($error !== null) {
                    $this->errors[$field][] = $error;
                }
            }
        }

        return $this->errors;
    }

    /**
     * Register a callable rule. A falsy return becomes a standard error.
     *
     * @param callable(mixed, ?string, array<string, mixed>): bool $callback
     */
    public function addCustomRule(string $name, callable $callback): void
    {
        $this->rules[$name] = $callback;
    }

    public function addRule(string $name, RuleInterface $rule): void
    {
        $this->rules[$name] = $rule;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Errors from the most recent validate() call; cleared on each new call.
     *
     * @return array<string, list<ValidationError>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function passed(): bool
    {
        return empty($this->errors);
    }

    /**
     * @return array{0: string, 1: string|null}
     */
    private function parseRule(string $rule): array
    {
        if (!str_contains($rule, ':')) {
            return [$rule, null];
        }
        [$ruleName, $parameter] = explode(':', $rule, 2);
        return [$ruleName, $parameter];
    }
}
