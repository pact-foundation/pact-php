<?php

namespace PhpPact\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class MinimalFormatter implements FormatterInterface
{
    /**
     * @return array<string, string>
     */
    public function format(MatcherInterface $matcher): array
    {
        return [
            'pact:matcher:type' => $matcher->getType(),
        ] + $matcher->getAttributes()->getData();
    }
}
