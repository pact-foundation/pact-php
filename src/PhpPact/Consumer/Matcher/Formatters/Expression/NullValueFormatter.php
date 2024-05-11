<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class NullValueFormatter extends AbstractExpressionFormatter
{
    public function format(MatcherInterface $matcher): string
    {
        return sprintf('matching(type, null)');
    }
}
