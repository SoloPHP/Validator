<?php

namespace Solo\Validator\Interfaces;

interface ValidatorInterface
{
   /**
    * Sets data for validation
    *
    * @param array $fields Associative array of field-value pairs to validate
    * @return void
    */
    public function collect(array $fields): void;

   /**
    * Starts validation chain for specific field
    *
    * @param string $field Name of the field to validate
    * @return $this
    */
    public function validate(string $field): self;

   /**
    * Returns validation errors
    *
    * @return array Associative array where key is field name and value is error message
    */
    public function getErrors(): array;

   /**
    * Checks if validation has failed
    *
    * @return bool True if there are any validation errors, false otherwise
    */
    public function failed(): bool;
}