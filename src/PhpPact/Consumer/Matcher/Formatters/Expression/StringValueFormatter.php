<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class StringValueFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof StringValue) {
            throw $this->getMatcherNotSupportedException($matcher);
        }

        return sprintf('matching(type, %s)', $this->normalize($matcher->getValue()));
    }
}
