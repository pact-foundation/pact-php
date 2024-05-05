<?php

namespace PhpPact\Consumer\Matcher\Model;

interface JsonFormatterInterface extends FormatterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array;
}
