<?php

namespace PhpPactTest\CompatibilitySuite\Model;

class VerifyResult
{
    public function __construct(private bool $success, private string $output)
    {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getOutput(): string
    {
        return $this->output;
    }
}
