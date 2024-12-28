<?php declare(strict_types=1);

namespace Solo\Validator;

use libphonenumber\PhoneNumberUtil;

trait RulesTrait
{
    public function required(?string $message = null): self
    {
        if (is_null($this->value) || (is_string($this->value) && trim($this->value) === '')) {
            $this->addError(__FUNCTION__, $message);
        }
        return $this;
    }

    public function unique(bool $isUnique, ?string $message = null): self
    {
        if (!$isUnique) {
            $this->addError(__FUNCTION__, $message);
        }
        return $this;
    }

    public function string(?string $message = null): self
    {
        if (!is_string($this->value)) {
            $this->addError(__FUNCTION__, $message);
        }
        return $this;
    }

    public function numeric(?string $message = null): self
    {
        if (!is_numeric($this->value)) {
            $this->addError(__FUNCTION__, $message);
        }
        return $this;
    }

    public function pattern(string $regex, ?string $message = null): self
    {
        $pattern = '/^(' . $regex . ')$/u';
        if (!preg_match($pattern, $this->value)) {
            $this->addError(__FUNCTION__, $message, ['{pattern}' => $regex]);
        }
        return $this;
    }

    public function phone(string $region = 'RU', ?string $message = null): self
    {
        if (PhoneNumberUtil::isViablePhoneNumber($this->value)) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $numberProto = $phoneUtil->parse($this->value, $region);
            if ($phoneUtil->isValidNumber($numberProto) === false) {
                $this->addError(__FUNCTION__, $message);
            }
        } else {
            $this->addError(__FUNCTION__, $message);
        }
        return $this;
    }

    public function email(?string $message = null): self
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->addError(__FUNCTION__, $message);
        }
        return $this;
    }

    public function positive(?string $message = null): self
    {
        if (!is_numeric($this->value) || $this->value < 1) {
            $this->addError(__FUNCTION__, $message);
        }
        return $this;
    }

    public function equal(string|int|float|bool $value, ?string $message = null): self
    {
        if ($this->value != $value) {
            $this->addError(__FUNCTION__, $message);
        }
        return $this;
    }
}