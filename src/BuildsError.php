<?php

declare(strict_types=1);

namespace Solo\Validator;

trait BuildsError
{
    /**
     * @return array{rule: string, params?: string[]}
     */
    private function buildError(string $rule, ?string $parameter): array
    {
        $error = ['rule' => $rule];
        if ($parameter !== null) {
            $error['params'] = explode(',', $parameter);
        }
        return $error;
    }
}
