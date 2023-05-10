<?php

namespace PhpPact\Consumer\Registry\Interaction;

use PhpPact\Consumer\Registry\Pact\PactRegistryInterface;
use PhpPact\FFI\ClientInterface;

abstract class AbstractRegistry implements RegistryInterface
{
    protected int $id;

    public function __construct(
        protected ClientInterface $client,
        protected PactRegistryInterface $pactRegistry
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    abstract protected function newInteraction(string $description): self;
}
