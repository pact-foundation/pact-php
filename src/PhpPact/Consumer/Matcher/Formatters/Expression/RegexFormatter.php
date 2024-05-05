<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class RegexFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof Regex) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $value = $matcher->getValue();
        if (!is_string($value)) {
            throw new InvalidValueException(sprintf("Regex formatter doesn't support value of type %s", gettype($value)));
        }

        return sprintf("matching(regex, %s, %s)", $this->normalize($matcher->getRegex()), $this->normalize($value));
    }
}
