<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * The field a rule is validating, within the whole payload.
 */
final readonly class Field
{
    /**
     * @param array<string, mixed> $data The whole payload, for cross-field rules
     */
    public function __construct(
        public string $name,
        public array $data,
    ) {
    }

    /**
     * Whether the payload has the field's key, a null value included.
     */
    public function present(): bool
    {
        return array_key_exists($this->name, $this->data);
    }
}
