<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * Marks a rule that checks presence/emptiness itself (e.g. required, filled).
 * Presence rules run even on blank values; every other rule is skipped for a
 * blank value of a nullable field.
 */
interface PresenceRule
{
}
