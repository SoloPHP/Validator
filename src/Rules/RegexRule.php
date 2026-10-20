<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\CastsToString;

final class RegexRule implements RuleInterface
{
    use CastsToString;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        $string = $this->castToString($value);
        if ($parameter === null || $string === null) {
            return ['rule' => 'regex'];
        }
        $result = @preg_match($parameter, $string);
        return ($result === 1) ? null : ['rule' => 'regex'];
    }
}
