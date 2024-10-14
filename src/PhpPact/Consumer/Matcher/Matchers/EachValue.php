<?php

namespace PhpPact\Consumer\Matcher\Matchers;

/**
 * Allows defining matching rules to apply to the values in a collection. For maps, delgates to the Values matcher.
 */
class EachValue extends AbstractValues
{
    protected function getType(): string
    {
        return 'eachValue';
    }
}
