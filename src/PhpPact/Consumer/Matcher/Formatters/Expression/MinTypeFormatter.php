<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\MinType;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class MinTypeFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof MinType) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        if ($matcher->isMatchingType()) {
            return sprintf('atLeast(%u), eachValue(matching(type, %s)', $matcher->getMin(), $this->normalize($matcher->getValue()));
        }

        return sprintf('atLeast(%u)', $matcher->getMin());
    }
}
