<?php

namespace PhpPact\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class ExpressionFormatter implements FormatterInterface
{
    public function format(MatcherInterface $matcher): Expression
    {
        if (!$matcher instanceof ExpressionFormattableInterface) {
            throw new MatcherNotSupportedException('Matcher does not support expression format');
        }

        return $matcher->formatExpression();
    }
}
