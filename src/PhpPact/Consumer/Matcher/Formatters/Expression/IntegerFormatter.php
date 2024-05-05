<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class IntegerFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof Integer) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $value = $matcher->getValue();
        if (!is_int($value)) {
            throw new InvalidValueException(sprintf("Integer formatter doesn't support value of type %s", gettype($value)));
        }

        return sprintf('matching(integer, %d)', $value);
    }
}
