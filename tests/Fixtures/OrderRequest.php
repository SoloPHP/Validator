<?php

declare(strict_types=1);

namespace Solo\Validator\Tests\Fixtures;

use Solo\RequestHandler\Attributes\Items;
use Solo\RequestHandler\Attributes\Validate;
use Solo\RequestHandler\Request;

final class OrderRequest extends Request
{
    /** @var list<OrderLineRequest> */
    #[Validate('required|array|min:1')]
    #[Items(OrderLineRequest::class)]
    public array $lines;
}
