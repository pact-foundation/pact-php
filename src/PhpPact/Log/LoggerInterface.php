<?php

namespace PhpPact\Log;

use PhpPact\Log\Model\SinkInterface;

interface LoggerInterface
{
    public function attach(SinkInterface $sink): void;

    public function apply(): void;

    public function fetchBuffer(): string;
}
