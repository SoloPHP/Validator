<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * Marks a rule whose single argument may contain commas (a date format, a
 * pattern): it gets everything after `:` as one parameter.
 *
 * @internal
 */
interface WholeArgumentRule
{
}
