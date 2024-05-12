<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\MinMaxType;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class MinMaxTypeFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof MinMaxType) {
            throw $this->getMatcherNotSupportedException($matcher);
        }

        return sprintf('atLeast(%u), atMost(%u), eachValue(matching(type, %s)', $matcher->getMin(), $matcher->getMax(), $this->normalize($matcher->getValue()));
    }
}
