<?php

declare(strict_types=1);

namespace Solo\Validator\Tests\Fixtures;

use Solo\RequestHandler\Attributes\Validate;
use Solo\RequestHandler\Request;

final class TypoRequest extends Request
{
    #[Validate('requird|string')]
    public string $name;
}
