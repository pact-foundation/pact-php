<?php

namespace PhpPact\Matchers\Checkers;

interface MatchCheckerInterface
{
    public function match($path, $expected, $actual, $matchingRules = []);
}
