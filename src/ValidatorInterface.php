<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * Interface ValidatorInterface
 *
 * Defines the contract for validation logic.
 */
interface ValidatorInterface
{
    /**
     * Validates the given data based on the provided rules.
     *
     * @param array $data The input data to be validated.
     * @param array $rules An associative array of validation rules.
     * @param array $customMessages (Optional) Custom error messages for validation rules.
     * @return array An array of validation errors, if any.
     */
    public function validate(array $data, array $rules, array $customMessages = []): array;

    /**
     * Checks if validation has failed.
     *
     * @return bool True if there are validation errors, false otherwise.
     */
    public function fails(): bool;

    /**
     * Retrieves the validation errors.
     *
     * @return array An associative array of validation errors.
     */
    public function errors(): array;

    /**
     * Checks if validation has passed.
     *
     * @return bool True if validation passed without errors, false otherwise.
     */
    public function passed(): bool;
}
