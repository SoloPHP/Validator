<?php

declare(strict_types=1);

namespace Solo\Validator\Rules;

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
        $parsed = $phoneUtil->parse($value, $parameter);

        return $phoneUtil->isValidNumber($parsed) ? null : ['rule' => 'phone'];
    }
}
