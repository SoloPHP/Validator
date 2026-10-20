<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * @phpstan-type ValidationError array{rule: string, params?: list<string>}
 */
interface RuleInterface
{
    /**
     * @param list<string> $params The rule's arguments as written: 'in:a,b' gives ['a', 'b'], 'email' gives []
     * @return ValidationError|null Null = pass; array = validation error
     */
    public function validate(mixed $value, array $params, Field $field): ?array;
}
