<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\FFI\ProxyInterface;

abstract class AbstractDriver implements DriverInterface
{
    protected int $id;

    public function __construct(
        protected ProxyInterface $proxy,
        protected PactDriverInterface $pactDriver
    ) {
    }
}
