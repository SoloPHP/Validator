<?php

declare(strict_types=1);

namespace Solo\Validator\Tests\Fixtures;

use Solo\RequestHandler\Attributes\Validate;
use Solo\RequestHandler\Request;

final class OrderLineRequest extends Request
{
    #[Validate('required|string|max:64')]
    public string $sku;

    #[Validate('required|integer|min_value:1')]
    public int $qty;
}
