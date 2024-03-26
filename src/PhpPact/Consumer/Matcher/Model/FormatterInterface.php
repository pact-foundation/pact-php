<?php

namespace PhpPact\Consumer\Matcher\Model;

interface FormatterInterface
{
    /**
     * @return string|array<string, mixed>
     */
    public function format(MatcherInterface $matcher): string|array;
}
