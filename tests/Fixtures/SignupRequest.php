<?php

declare(strict_types=1);

namespace Solo\Validator\Tests\Fixtures;

use Solo\RequestHandler\Attributes\Validate;
use Solo\RequestHandler\Request;

final class SignupRequest extends Request
{
    #[Validate('required | email')]
    public string $email;

    #[Validate('in:individual,company')]
    public string $type = 'individual';

    #[Validate('nullable|string|max:255|required_if:type,company')]
    public ?string $company = null;
}
