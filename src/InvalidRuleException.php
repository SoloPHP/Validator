<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * A rule string that cannot be validated against: an unknown rule or
 * parameters the rule does not accept. A configuration error, not bad input.
 */
final class InvalidRuleException extends \LogicException
{
    public static function unknownRule(string $name, string $rules): self
    {
        return new self(sprintf(
            "Unknown validation rule '%s' in '%s'. Register custom rules in the Validator constructor.",
            $name,
            $rules,
        ));
    }

    public static function invalidName(string $name): self
    {
        return new self(sprintf(
            "Cannot register rule '%s': no rule string can refer to it ('nullable' is reserved; a name cannot"
            . " be empty or contain '|', ':' or surrounding whitespace).",
            $name,
        ));
    }

    /**
     * For ParameterizedRule::checkParams(): what the rule expects ('one
     * number'). The validator adds the rule as written and its rule string.
     */
    public static function invalidParams(string $expected): self
    {
        return new self("expects {$expected}");
    }

    /**
     * A parameter error, located in its rule string.
     *
     * @internal
     */
    public static function at(string $segment, string $rules, self $previous): self
    {
        $where = $segment === $rules ? "'{$segment}'" : "'{$segment}' in '{$rules}'";

        return new self("Invalid rule {$where}: " . rtrim($previous->getMessage(), '.') . '.', 0, $previous);
    }
}
