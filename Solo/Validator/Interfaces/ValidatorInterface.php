<?php

namespace Solo\Validator\Interfaces;

interface ValidatorInterface
{
   /**
    * Starts validation chain for specific field
    *
    * @param string $field Field name to validate
    * @param mixed $value Field value to validate
    * @return self New validator instance
    */
    public function validate(string $field, mixed $value): self;

   /**
    * Returns validation errors
    *
    * @return array Field name => error message pairs
    */
    public function getErrors(): array;

   /**
    * Checks if validation has failed
    *
    * @return bool True if errors exist
    */
    public function failed(): bool;
}