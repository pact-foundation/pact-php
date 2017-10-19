<?php

namespace PhpPact\Matchers\Checkers;

interface IMatchChecker
{
    public function Match($path, $expected, $actual);
}
