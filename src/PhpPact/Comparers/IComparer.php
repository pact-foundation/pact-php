<?php

namespace PhpPact\Comparers;

interface IComparer
{
    public function compare($expected, $actual);
}
