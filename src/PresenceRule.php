<?php

declare(strict_types=1);

namespace Solo\Validator;

/**
 * Marks a rule that checks presence/emptiness itself (e.g. required, filled,
 * required_if). Presence rules run for every field; every other rule is
 * skipped for an absent field and for a blank value of a nullable field.
 */
interface PresenceRule
{
}
