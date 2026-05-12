<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * @phpstan-type ValidationError array{rule: string, params?: string[]}
 */
interface RuleInterface
{
    /**
     * @param array<string, mixed> $data Full payload (for cross-field rules)
     * @return ValidationError|null Null = pass; array = validation error
     */
    public function validate(mixed $value, ?string $parameter, array $data = []): ?array;
}
