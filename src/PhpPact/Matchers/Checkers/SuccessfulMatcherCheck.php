<?php

namespace PhpPact\Matchers\Checkers;

class SuccessfulMatcherCheck extends MatcherCheck
{
    public function __construct($path)
    {
        $this->setPath($path);
    }
}
