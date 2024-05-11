<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\Equality;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class EqualityFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof Equality) {
            throw $this->getMatcherNotSupportedException($matcher);
        }

        return sprintf('matching(equalTo, %s)', $this->normalize($matcher->getValue()));
    }
}
