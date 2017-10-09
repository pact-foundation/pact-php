<?php

namespace PhpPact\Comparers;

interface IComparer
{
    public function Compare($expected, $actual);
}
