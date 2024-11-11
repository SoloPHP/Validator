<?php declare(strict_types=1);

namespace Solo\Validator;

trait MessagesTrait
{
    private array $messages = [
        'field' => 'Field {field} does not exist',
        'required' => 'Field {field} is required',
        'unique' => 'Field {field} must be a unique',
        'string' => 'Field {field} must be a string',
        'numeric' => 'Field {field} must be a number',
        'phone' => 'Field {field} must be a valid phone number',
        'email' => 'Field {field} must be a valid email address',
        'pattern' => 'Field {field} must match the regular expression {pattern}'
    ];
}