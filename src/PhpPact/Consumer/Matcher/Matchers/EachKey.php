<?php

namespace PhpPact\Consumer\Matcher\Matchers;

/**
 * Allows defining matching rules to apply to the keys in a map
 */
class EachKey extends AbstractValues
{
    protected function getType(): string
    {
        return 'eachKey';
    }
}
