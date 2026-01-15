---
layout: home

hero:
  name: Solo Validator
  text: PHP Validation Library
  tagline: Lightweight, flexible validation with custom rules and messages.
  image:
    src: /logo.svg
    alt: Solo Validator
  actions:
    - theme: brand
      text: Get Started
      link: /guide/installation
    - theme: alt
      text: View on GitHub
      link: https://github.com/solophp/validator

features:
  - icon: ✅
    title: Built-in Rules
    details: Required, email, phone, min, max, length, numeric, date, and more.
  - icon: 🔧
    title: Custom Rules
    details: Extend validation with your own rules via simple callbacks.
  - icon: 💬
    title: Custom Messages
    details: Override messages globally or per-field with placeholder support.
  - icon: 🎯
    title: Parameterized Rules
    details: Define rules with parameters like min:8, max:100, in:a,b,c.
  - icon: 📱
    title: Phone Validation
    details: International phone number validation via libphonenumber.
  - icon: 🪶
    title: Lightweight
    details: Minimal dependencies, PSR-4 compliant, easy to integrate.
---

<style>
:root {
  --vp-home-hero-name-color: transparent;
  --vp-home-hero-name-background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
  --vp-home-hero-image-background-image: linear-gradient(135deg, #8b5cf630 0%, #6366f130 100%);
  --vp-home-hero-image-filter: blur(44px);
}

.VPHero .VPImage {
  max-width: 200px;
  max-height: 200px;
}
</style>

## Quick Example

```php
use Solo\Validator\Validator;

$validator = new Validator();

$data = [
    'email' => 'user@example.com',
    'username' => 'john_doe',
    'age' => 25,
];

$rules = [
    'email' => 'required|email',
    'username' => 'required|min:3|max:20',
    'age' => 'integer|min_value:18',
];

$errors = $validator->validate($data, $rules);

if ($validator->fails()) {
    print_r($validator->errors());
} else {
    echo "Validation passed!";
}
```

## Installation

```bash
composer require solophp/validator
```

**Requirements:** PHP 8.1+
