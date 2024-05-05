<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Matchers\MatchAll;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class MatchAllFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        if (!$matcher instanceof MatchAll) {
            throw $this->getMatcherNotSupportedException($matcher);
        }
        $matchers = $matcher->getMatchers();
        if (empty($matchers)) {
            throw new MatchingExpressionException("Matcher 'matchAll' need at least 1 matchers");
        }

        return implode(', ', array_map(
            fn (MatcherInterface $matcher): string => $matcher->createExpressionFormatter()->format($matcher),
            $matchers
        ));
    }
}
