<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Solo\Validator\Field;
use Solo\Validator\InvalidRuleException;
use Solo\Validator\ParameterizedRule;
use Solo\Validator\RuleInterface;

/**
 * phone - a valid number in international format (+14155552671);
 * phone:US - a number without a country code is read as a US one.
 * Needs giggsey/libphonenumber-for-php-lite.
 */
final class PhoneRule implements RuleInterface, ParameterizedRule
{
    public function __construct()
    {
        if (!class_exists(PhoneNumberUtil::class)) {
            throw new InvalidRuleException(
                "Rule 'phone' needs libphonenumber: composer require giggsey/libphonenumber-for-php-lite"
            );
        }
    }

    public function checkParams(array $params): void
    {
        if ($params === []) {
            return;
        }

        $regions = PhoneNumberUtil::getInstance()->getSupportedRegions();
        if (count($params) > 1 || !in_array(strtoupper($params[0]), $regions, true)) {
            throw InvalidRuleException::invalidParams('an optional region code such as US');
        }
    }

    public function validate(mixed $value, array $params, Field $field): ?array
    {
        return is_string($value) && self::isValid($value, isset($params[0]) ? strtoupper($params[0]) : null)
            ? null
            : Params::error('phone', $params);
    }

    private static function isValid(string $number, ?string $region): bool
    {
        if (!PhoneNumberUtil::isViablePhoneNumber($number)) {
            return false;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            return $phoneUtil->isValidNumber($phoneUtil->parse($number, $region));
        } catch (NumberParseException) {
            // E.g. a national number without a region, or an unknown country code.
            return false;
        }
    }
}
