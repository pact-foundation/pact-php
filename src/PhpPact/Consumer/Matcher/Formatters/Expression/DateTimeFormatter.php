<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Matchers\AbstractDateTime;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class DateTimeFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof AbstractDateTime) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $value = $matcher->getValue();
        if (!is_string($value)) {
            throw new InvalidValueException(sprintf("DateTime formatter doesn't support value of type %s", gettype($value)));
        }

        return sprintf("matching(%s, %s, %s)", $matcher->getType(), $this->normalize($matcher->getFormat()), $this->normalize($value));
    }
}
