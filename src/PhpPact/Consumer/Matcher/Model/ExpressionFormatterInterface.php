<?php

namespace PhpPact\Consumer\Matcher\Model;

interface ExpressionFormatterInterface extends FormatterInterface
{
    public function format(MatcherInterface $matcher): string;
}
