<?php

namespace Solo\Validator\Interfaces;

interface RulesInterface
{
    /**
     * Checks if value is not empty
     *
     * @param string|null $message Custom error message
     * @return $this
     */
    public function required(?string $message = null): self;

    /**
     * Checks if value is unique
     *
     * @param bool $isUnique Result of uniqueness check
     * @param string|null $message Custom error message
     * @return $this
     */
    public function unique(bool $isUnique, ?string $message = null): self;

    /**
     * Checks if value is string
     *
     * @param string|null $message Custom error message
     * @return $this
     */
    public function string(?string $message = null): self;

    /**
     * Checks if value is numeric
     *
     * @param string|null $message Custom error message
     * @return $this
     */
    public function numeric(?string $message = null): self;

    /**
     * Validates if value matches the pattern
     *
     * @param string $regex Regular expression without delimiters
     * @param string|null $message Custom error message
     * @return $this
     */
    public function pattern(string $regex, ?string $message = null): self;

    /**
     * Validates if value is a valid phone number
     *
     * @param string $region Region code (ISO 3166-1 alpha-2)
     * @param string|null $message Custom error message
     * @return $this
     */
    public function phone(string $region = 'RU', ?string $message = null): self;

    /**
     * Checks if value is valid email address
     *
     * @param string|null $message Custom error message
     * @return $this
     */
    public function email(?string $message = null): self;

    /**
     * Checks if value is positive number
     *
     * @param string|null $message Custom error message
     * @return $this
     */
    public function positive(?string $message = null): self;

    /**
     * Checks if value equals to provided value
     *
     * @param string|int|float|bool $value Value to compare with
     * @param string|null $message Custom error message
     * @return $this
     */
    public function equal(string|int|float|bool $value, ?string $message = null): self;
}