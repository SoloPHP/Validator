<?php declare(strict_types=1);

namespace Solo\Validator;

use libphonenumber\PhoneNumberUtil;

trait RulesTrait
{
    public function required(): self
    {
        if (is_null($this->value) || (is_string($this->value) && trim($this->value) === '')) {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }

    public function unique(bool $isUnique): self
    {
        if (!$isUnique) {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }

    public function string(): self
    {
        if (!is_string($this->value)) {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }

    public function numeric(): self
    {
        if (!is_numeric($this->value)) {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }

    public function pattern(string $regex): self
    {
        $pattern = '/^(' . $regex . ')$/u';
        if (!preg_match($pattern, $this->value)) {
            $this->addError(__FUNCTION__, ['{pattern}' => $regex]);
        }
        return $this;
    }

    public function phone(string $region = 'RU'): self
    {
        if (PhoneNumberUtil::isViablePhoneNumber($this->value)) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $numberProto = $phoneUtil->parse($this->value, $region);
            if ($phoneUtil->isValidNumber($numberProto) === false) {
                $this->addError(__FUNCTION__);
            }
        } else {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }

    public function email(): self
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }

    public function positive(): self
    {
        if (!is_numeric($this->value) || $this->value < 1) {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }
}