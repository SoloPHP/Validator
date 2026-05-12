<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\BuildsError;

final class DateRule implements RuleInterface
{
    use BuildsError;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if (!is_string($value) && !is_numeric($value)) {
            return ['rule' => 'date'];
        }

        if ($parameter !== null) {
            if (!is_string($value)) {
                return $this->buildError('date_format', $parameter);
            }
            $parsed = \DateTime::createFromFormat($parameter, $value);
            return ($parsed !== false && $parsed->format($parameter) === $value)
                ? null
                : $this->buildError('date_format', $parameter);
        }

        try {
            $dateString = is_numeric($value) ? '@' . $value : (string)$value;
            new \DateTime($dateString);
            return null;
        } catch (\Exception) {
            return ['rule' => 'date'];
        }
    }
}
