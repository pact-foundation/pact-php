<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Matchers\NotEmpty;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class NotEmptyFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof NotEmpty) {
            throw $this->getMatcherNotSupportedException($matcher);
        }

        return sprintf('notEmpty(%s)', $this->normalize($matcher->getValue()));
    }
}
