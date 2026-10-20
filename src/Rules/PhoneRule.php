<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Solo\Validator\RuleInterface;

final class PhoneRule implements RuleInterface
{
    public function validate(mixed $value, ?string $parameter, array $data = []): ?array
    {
        if (!is_string($value) || !PhoneNumberUtil::isViablePhoneNumber($value)) {
            return ['rule' => 'phone'];
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $parsed = $phoneUtil->parse($value, $parameter);
        } catch (NumberParseException) {
            // E.g. a national number without a valid region, or an unknown country code.
            return ['rule' => 'phone'];
        }

        return $phoneUtil->isValidNumber($parsed) ? null : ['rule' => 'phone'];
    }
}
