<?php

namespace PhpPact\Plugin\Driver\Pact;

use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Plugin\Exception\PluginNotSupportedBySpecificationException;

abstract class AbstractPluginPactDriver extends PactDriver
{
    public function cleanUp(): void
    {
        $this->validatePact();
        $this->client->call('pactffi_cleanup_plugins', $this->pact->handle);
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

        $this->client->call('pactffi_using_plugin', $this->pact->handle, $this->getPluginName(), $this->getPluginVersion());

        return $this;
    }
}
