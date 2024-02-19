<?php

namespace PhpPact\Consumer\Model\Pact;

class Pact
{
    public function __construct(public readonly int $handle)
    {
    }
}
