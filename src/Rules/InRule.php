<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\BuildsError;
use Solo\Validator\CastsToString;

final class InRule implements RuleInterface
{
    use BuildsError;
    use CastsToString;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if ($parameter === null) {
            return ['rule' => 'in'];
        }

        $allowedSet = array_flip(explode(',', $parameter));
        $items = is_array($value) ? $value : [$value];

        foreach ($items as $item) {
            $string = $this->castToString($item);
            if ($string === null || !isset($allowedSet[$string])) {
                return $this->buildError('in', $parameter);
            }
        }

        return null;
    }
}
