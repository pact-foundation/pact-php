<?php

namespace PhpPactTest\CompatibilitySuite\Model;

use PhpPact\Service\LoggerInterface;

class Logger implements LoggerInterface
{
    private string $output;

    public function log(string $output): void
    {
        $this->output = $output;
    }

    public function getOutput(): string
    {
        return $this->output;
    }
}
