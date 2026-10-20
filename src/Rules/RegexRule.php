<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use Solo\Validator\Field;
use Solo\Validator\InvalidRuleException;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\RuleInterface;
use Solo\Validator\Value;
use Solo\Validator\WholeArgumentRule;

/**
 * The pattern may contain commas but not `|`, which splits the rule string:
 * register a callable rule for an alternation.
 */
final class RegexRule implements RuleInterface, ParameterizedRule, WholeArgumentRule
{
    public function checkParams(array $params): void
    {
        if ($params === [] || @preg_match($params[0], '') === false) {
            throw InvalidRuleException::invalidParams('a valid PCRE pattern');
        }
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        $string = Value::toString($value);

        return $string !== null && preg_match($params[0], $string) === 1 ? null : ['rule' => 'regex'];
    }
}
