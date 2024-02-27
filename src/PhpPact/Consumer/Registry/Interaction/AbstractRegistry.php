<?php

namespace PhpPact\Consumer\Registry\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\FFI\ClientInterface;

abstract class AbstractRegistry implements RegistryInterface
{
    protected int $id;

    public function __construct(
        protected ClientInterface $client,
        protected PactDriverInterface $pactDriver
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    abstract protected function newInteraction(string $description): self;
}
