<?php

namespace PhpPact\Xml\Model\Builder;

use PhpPact\Xml\Model\Matcher\Matcher;

trait MatcherTrait
{
    public function matcherType(string $type): callable
    {
        return fn (Matcher $matcher) => $matcher->setType($type);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function matcherOptions(array $options): callable
    {
        return fn (Matcher $matcher) => $matcher->setOptions($options);
    }
}
