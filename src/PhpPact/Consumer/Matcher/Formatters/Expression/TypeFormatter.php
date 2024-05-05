<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class TypeFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof Type) {
            throw $this->getMatcherNotSupportedException($matcher);
        }

        return sprintf('matching(type, %s)', $this->normalize($matcher->getValue()));
    }
}
