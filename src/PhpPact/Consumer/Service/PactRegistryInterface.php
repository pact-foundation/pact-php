<?php

namespace PhpPact\Consumer\Service;

interface PactRegistryInterface
{
    public function registerPact(): void;

    public function getId(): int;

    public function writePact(): void;

    public function cleanUp(): void;
}
