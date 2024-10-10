<?php

namespace PhpPact\Consumer\Matcher\Model;

interface FormatterInterface
{
    public function format(MatcherInterface $matcher): mixed;
}
