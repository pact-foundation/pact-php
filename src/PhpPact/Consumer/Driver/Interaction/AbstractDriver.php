<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\FFI\ClientInterface;

abstract class AbstractDriver implements DriverInterface
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
}
