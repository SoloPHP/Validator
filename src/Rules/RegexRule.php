<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;

final class RegexRule implements RuleInterface
{
    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if ($parameter === null) {
            return ['rule' => 'regex'];
        }
        $result = @preg_match($parameter, (string)$value);
        return ($result === 1) ? null : ['rule' => 'regex'];
    }
}
