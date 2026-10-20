<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Closure;
use Solo\Validator\Field;
use Solo\Validator\RuleInterface;

/**
 * A closure registered in the Validator constructor.
 *
 * @internal
 */
final class CallbackRule implements RuleInterface
{
    /**
     * @param Closure(mixed, list<string>, Field): bool $check
     */
    public function __construct(
        private readonly string $name,
        private readonly Closure $check,
    ) {
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return ($this->check)($value, $params, $field) ? null : Params::error($this->name, $params);
    }
}
