<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Matchers\Number;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class NumberFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof Number) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $value = $matcher->getValue();
        if (null === $value) {
            throw new InvalidValueException(sprintf("Number formatter doesn't support value of type %s", gettype($value)));
        }

        return sprintf('matching(number, %s)', $this->normalize($value));
    }
}
