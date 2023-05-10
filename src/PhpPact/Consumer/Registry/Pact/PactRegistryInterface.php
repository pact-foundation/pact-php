<?php

namespace PhpPact\Consumer\Registry\Pact;

interface PactRegistryInterface
{
    public function registerPact(string $consumer, string $provider, int $specification): void;

    public function getId(): int;

    public function deletePact(): void;
}
