<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\MatchingField;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class MatchingFieldFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof MatchingField) {
            throw $this->getMatcherNotSupportedException($matcher);
        }

        return sprintf("matching($%s)", $this->normalize($matcher->getFieldName()));
    }
}
