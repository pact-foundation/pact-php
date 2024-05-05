<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Matchers\Decimal;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class DecimalFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof Decimal) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $value = $matcher->getValue();
        if (!is_float($value)) {
            throw new InvalidValueException(sprintf("Decimal formatter doesn't support value of type %s", gettype($value)));
        }

        return sprintf('matching(decimal, %s)', $this->normalize($value));
    }
}
