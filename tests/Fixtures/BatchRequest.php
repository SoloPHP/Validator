<?php

declare(strict_types=1);

namespace Solo\Validator\Tests\Fixtures;

use Solo\RequestHandler\Attributes\Validate;
use Solo\RequestHandler\Request;

final class BatchRequest extends Request
{
    /** An empty list is an answer; a missing one is not. */
    #[Validate('present|array')]
    public array $ids;
}
