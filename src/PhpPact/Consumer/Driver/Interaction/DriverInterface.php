<?php

namespace PhpPact\Consumer\Driver\Interaction;

interface DriverInterface
{
    public function newInteraction(string $description): void;
}
