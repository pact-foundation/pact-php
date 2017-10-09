<?php

namespace PhpPact\Matchers;

class SuccessfulMatcherCheck extends MatcherCheck
{
    public function __construct($path)
    {
        $this->setPath($path);
    }
}
