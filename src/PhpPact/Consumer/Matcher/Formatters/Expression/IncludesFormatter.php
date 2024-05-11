<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\Includes;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class IncludesFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof Includes) {
            throw $this->getMatcherNotSupportedException($matcher);
        }

        return sprintf('matching(include, %s)', $this->normalize($matcher->getValue()));
    }
}
