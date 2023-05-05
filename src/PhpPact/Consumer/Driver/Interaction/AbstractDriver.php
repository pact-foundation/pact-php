<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Service\FFIInterface;

abstract class AbstractDriver implements DriverInterface
{
    protected int $id;

    public function __construct(
        protected FFIInterface $ffi,
        protected PactDriverInterface $pactDriver
    ) {
    }
}
