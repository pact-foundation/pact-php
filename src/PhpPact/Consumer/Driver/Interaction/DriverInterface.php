<?php

namespace PhpPact\Consumer\Driver\Interaction;

interface DriverInterface
{
    public function getId(): int;

    public function newInteraction(string $description): void;
}
