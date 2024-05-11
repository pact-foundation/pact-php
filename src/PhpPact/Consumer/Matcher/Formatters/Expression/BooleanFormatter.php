<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Matchers\Boolean;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class BooleanFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof Boolean) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $value = $matcher->getValue();
        if (!is_bool($value)) {
            throw new InvalidValueException(sprintf("Boolean formatter doesn't support value of type %s", gettype($value)));
        }

        return sprintf('matching(boolean, %s)', $this->normalize($value));
    }
}
