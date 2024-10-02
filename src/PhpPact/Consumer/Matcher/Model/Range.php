<?php

namespace PhpPact\Consumer\Matcher\Model;

class Range
{
    public function __construct(public readonly int $min, public readonly int $max)
    {
    }
}
