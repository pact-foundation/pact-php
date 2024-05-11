<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class MaxTypeFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof MaxType) {
            throw $this->getMatcherNotSupportedException($matcher);
        }

        return sprintf('atMost(%u)', $matcher->getMax());
    }
}
