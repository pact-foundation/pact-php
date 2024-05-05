<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class EachValueFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof EachValue) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $rules = $matcher->getRules();
        if (count($rules) !== 1) {
            throw new MatchingExpressionException(sprintf("Matcher 'eachValue' only support 1 rule in expression, %d provided", count($rules)));
        }
        $rule = reset($rules);

        return sprintf('eachValue(%s)', $rule->createExpressionFormatter()->format($rule));
    }
}
