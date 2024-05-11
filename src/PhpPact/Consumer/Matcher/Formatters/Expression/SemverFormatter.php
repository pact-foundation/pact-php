<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Matchers\Semver;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class SemverFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof Semver) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $value = $matcher->getValue();
        if (!is_string($value)) {
            throw new InvalidValueException(sprintf("Semver formatter doesn't support value of type %s", gettype($value)));
        }

        return sprintf('matching(semver, %s)', $this->normalize($value));
    }
}
