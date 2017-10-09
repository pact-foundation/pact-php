<?php

namespace PhpPact\Matchers;

interface IMatcher
{
    public function Match($path, $expected, $actual);
}
