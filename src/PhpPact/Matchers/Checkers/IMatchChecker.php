<?php

namespace PhpPact\Matchers\Checkers;

interface IMatchChecker
{
    public function match($path, $expected, $actual, $matchingRules = array());
}
