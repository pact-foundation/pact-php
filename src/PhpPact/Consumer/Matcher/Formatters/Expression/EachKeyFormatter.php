<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class EachKeyFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof EachKey) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $rules = $matcher->getRules();
        if (count($rules) !== 1) {
            throw new MatchingExpressionException(sprintf("Matcher 'eachKey' only support 1 rule in expression, %d provided", count($rules)));
        }
        $rule = reset($rules);

        return sprintf('eachKey(%s)', $rule->createExpressionFormatter()->format($rule));
    }
}
