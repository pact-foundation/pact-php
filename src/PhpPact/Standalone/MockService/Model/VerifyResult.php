<?php

namespace PhpPact\Standalone\MockService\Model;

class VerifyResult
{
    public function __construct(public readonly bool $matched, public readonly string $mismatches)
    {
    }
}
