<?php

namespace PhpPact\Consumer\Matcher;

interface MatcherInterface extends \JsonSerializable
{
    public function getValue();
}
