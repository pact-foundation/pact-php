<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Exception\MissingPactException;
use PhpPact\Consumer\Model\Pact\Pact;

abstract class AbstractDriver implements DriverInterface
{
    protected ?Pact $pact = null;

    public function __construct(protected PactDriverInterface $pactDriver)
    {
        $this->pactDriver->initWithLogLevel();
        $this->pact = $this->pactDriver->newPact();
    }

    public function writePactAndCleanUp(): bool
    {
        $this->validatePact();
        $this->writePact();
        $this->deletePact();

        return true;
    }

    protected function validatePact(): void
    {
        if (!$this->pact) {
            throw new MissingPactException();
        }
    }

    protected function deletePact(): void
    {
        $this->pactDriver->deletePact($this->pact);
        $this->pact = null;
    }

    protected function writePact(): void
    {
        $this->pactDriver->writePact($this->pact);
    }
}
