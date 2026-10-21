<?php

declare(strict_types=1);

namespace Solo\Validator;

use Closure;
use Solo\Contracts\Validator\ValidatorInterface;

/**
 * Each distinct rule string is compiled once per instance: parsed, its rules
 * resolved and their parameters checked, so an unknown rule or a bad parameter
 * fails on first use. validate() keeps no state; one instance can be shared.
 *
 * @phpstan-import-type ValidationError from RuleInterface
 * @phpstan-type Chain list<array{RuleInterface, list<string>}>
 * @phpstan-type CompiledRules array{bool, Chain, Chain}
 */
final class Validator implements ValidatorInterface
{
    /**
     * Distinct rule strings kept compiled. Past it the oldest is dropped, so
     * rule strings built per request cannot grow a long-lived instance forever.
     */
    private const COMPILED_LIMIT = 1000;

    /** @var array<string, RuleInterface> */
    private array $rules = [];

    /** @var array<string, CompiledRules> */
    private array $compiled = [];

    /**
     * @param array<string, RuleInterface|callable(mixed, list<string>, Field): bool> $rules
     *        Custom rules by name; a built-in rule's name overrides it. A callable
     *        returning false fails with ['rule' => name, 'params' => its params].
     * @throws InvalidRuleException A name no rule string can refer to
     * @throws \TypeError A rule that is neither a RuleInterface nor a callable
     */
    public function __construct(array $rules = [])
    {
        foreach ($rules as $name => $rule) {
            $name = (string)$name;
            if (self::parse($name) !== [false, [[$name, '', $name]]]) {
                throw InvalidRuleException::invalidName($name);
            }
            $this->rules[$name] = $rule instanceof RuleInterface
                ? $rule
                : new Rules\CallbackRule($name, Closure::fromCallable($rule));
        }
    }

    /**
     * An absent field is checked by presence rules only, and so is a blank
     * value (null, '', []) of a nullable field. The first failing rule ends
     * the field, so each field reports at most one error.
     *
     * @return array<string, list<ValidationError>>
     * @throws InvalidRuleException An unknown rule or invalid parameters
     */
    public function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $name => $ruleString) {
            [$nullable, $chain, $presenceChain] = $this->compiled[$ruleString] ?? $this->compile($ruleString);
            $value = $data[$name] ?? null;
            if (($value === null && !array_key_exists($name, $data)) || ($nullable && Value::isBlank($value))) {
                $chain = $presenceChain;
            }
            if ($chain === []) {
                continue;
            }

            $field = new Field((string)$name, $data);
            foreach ($chain as [$rule, $params]) {
                $error = $rule->validate($value, $params, $field);
                if ($error !== null) {
                    $errors[$name] = [$error];
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * @return CompiledRules The nullable flag, every rule, and the presence rules alone
     */
    private function compile(string $ruleString): array
    {
        [$nullable, $segments] = self::parse($ruleString);
        $chain = [];
        $presenceChain = [];

        foreach ($segments as [$name, $args, $segment]) {
            $rule = $this->rules[$name] ??= self::builtIn($name)
                ?? throw InvalidRuleException::unknownRule($name, $ruleString);
            $params = match (true) {
                $args === '' => [],
                $rule instanceof WholeArgumentRule => [$args],
                default => explode(',', $args),
            };
            if ($rule instanceof ParameterizedRule) {
                try {
                    $rule->checkParams($params);
                } catch (InvalidRuleException $e) {
                    throw InvalidRuleException::at($segment, $ruleString, $e);
                }
            }

            $chain[] = [$rule, $params];
            if ($rule instanceof PresenceRule) {
                $presenceChain[] = [$rule, $params];
            }
        }

        if (count($this->compiled) >= self::COMPILED_LIMIT) {
            unset($this->compiled[array_key_first($this->compiled)]);
        }

        return $this->compiled[$ruleString] = [$nullable, $chain, $presenceChain];
    }

    /**
     * Split a rule string the way Request-Handler's RuleParser does: on `|`,
     * trimmed, skipping empty segments; a rule's name ends at the first `:`.
     * `nullable` is a flag, not a rule.
     *
     * @return array{bool, list<array{string, string, string}>} The flag, and each rule's name, arguments and segment
     */
    private static function parse(string $ruleString): array
    {
        $nullable = false;
        $segments = [];

        foreach (explode('|', $ruleString) as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            [$name, $args] = explode(':', $segment, 2) + [1 => ''];
            if ($name === 'nullable') {
                $nullable = true;
                continue;
            }
            $segments[] = [$name, $args, $segment];
        }

        return [$nullable, $segments];
    }

    private static function builtIn(string $name): ?RuleInterface
    {
        return match ($name) {
            'required' => new Rules\RequiredRule(),
            'present' => new Rules\PresentRule(),
            'required_if' => new Rules\RequiredIfRule(),
            'required_unless' => new Rules\RequiredIfRule(unless: true),
            'required_with' => new Rules\RequiredWithRule(),
            'required_with_all' => new Rules\RequiredWithRule(all: true),
            'required_without' => new Rules\RequiredWithRule(without: true),
            'required_without_all' => new Rules\RequiredWithRule(all: true, without: true),
            'filled' => new Rules\FilledRule(),
            'prohibited_if' => new Rules\ProhibitedIfRule(),
            'prohibited_unless' => new Rules\ProhibitedIfRule(unless: true),
            'string' => new Rules\StringRule(),
            'integer' => new Rules\IntegerRule(),
            'numeric' => new Rules\NumericRule(),
            'boolean' => new Rules\BooleanRule(),
            'array' => new Rules\ArrayRule(),
            'email' => new Rules\EmailRule(),
            'phone' => new Rules\PhoneRule(),
            'uuid' => new Rules\UuidRule(),
            'date' => new Rules\DateRule(),
            'regex' => new Rules\RegexRule(),
            'in' => new Rules\InRule(),
            'min' => new Rules\MinRule(),
            'max' => new Rules\MaxRule(),
            'length' => new Rules\LengthRule(),
            'min_value' => new Rules\MinValueRule(),
            'max_value' => new Rules\MaxValueRule(),
            default => null,
        };
    }
}
