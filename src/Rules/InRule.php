<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\RuleInterface;
use Solo\Validator\BuildsError;

final class InRule implements RuleInterface
{
    use BuildsError;

    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if ($parameter === null) {
            return ['rule' => 'in'];
        }

        $allowedSet = array_flip(explode(',', $parameter));
        $items = is_array($value) ? $value : [$value];

        foreach ($items as $item) {
            if (!isset($allowedSet[(string)$item])) {
                return $this->buildError('in', $parameter);
            }
        }

        return null;
    }
}
