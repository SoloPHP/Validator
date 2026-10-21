---
layout: home

hero:
  name: Solo Validator
  text: PHP Validation Library
  tagline: Rule strings compiled once, strict about configuration, one structured error per field.
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
    details: required, required_if / with / without, prohibited_if, present, email, phone, uuid, date, min, max, in, regex and more.
  - icon: 🛑
    title: Fails Fast
    details: An unknown rule or a bad parameter throws on first use instead of silently validating nothing.
  - icon: 📦
    title: Structured Errors
    details: At most one machine-readable error per field, with the rule name and its parameters.
  - icon: 🔧
    title: Custom Rules
    details: Callables or classes, registered in the constructor, with access to the whole payload.
  - icon: ⚡
    title: Compiled and Stateless
    details: Each rule string is parsed once; validate() keeps no state, so one instance serves every request.
  - icon: 🤝
    title: Request-Handler Ready
    details: The validator behind solophp/request-handler 4, with the same rule grammar and reading of types.
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

$errors = $validator->validate(
    ['email' => 'user@example.com', 'username' => 'john_doe', 'age' => 25],
    [
        'email' => 'required|email',
        'username' => 'required|min:3|max:20',
        'age' => 'integer|min_value:18',
    ],
);

if ($errors === []) {
    echo "Validation passed!";
} else {
    // ['field' => [['rule' => 'email']], ...]
    print_r($errors);
}
```

## Installation

```bash
composer require solophp/validator
```

**Requirements:** PHP 8.3+
