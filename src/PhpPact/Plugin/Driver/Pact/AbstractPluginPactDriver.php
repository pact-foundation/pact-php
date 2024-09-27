<?php

namespace PhpPact\Plugin\Driver\Pact;

use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Plugin\Exception\PluginNotLoadedException;
use PhpPact\Plugin\Exception\PluginNotSupportedBySpecificationException;

abstract class AbstractPluginPactDriver extends PactDriver
{
    public function cleanUp(): void
    {
        $this->validatePact();
        $this->client->cleanupPlugins($this->pact->handle);
        parent::cleanUp();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->usingPlugin();
    }

    abstract protected function getPluginName(): string;

    protected function getPluginVersion(): ?string
    {
        return null;
    }

    private function usingPlugin(): self
    {
        if ($this->getSpecification() < $this->client->getPactSpecificationV4()) {
            throw new PluginNotSupportedBySpecificationException($this->config->getPactSpecificationVersion());
        }

        $error = $this->client->usingPlugin($this->pact->handle, $this->getPluginName(), $this->getPluginVersion());
        if ($error) {
            throw new PluginNotLoadedException($error);
        }

        return $this;
    }
}
