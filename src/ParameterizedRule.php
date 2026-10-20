<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * A rule that checks its parameters when a rule string is first compiled, so
 * a misconfigured rule ('min:abc') fails on its first use, whatever the input.
 */
interface ParameterizedRule
{
    /**
     * @param list<string> $params
     * @throws InvalidRuleException
     */
    public function checkParams(array $params): void;
}
