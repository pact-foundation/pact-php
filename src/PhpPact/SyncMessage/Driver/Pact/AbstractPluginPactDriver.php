<?php

namespace PhpPact\SyncMessage\Driver\Pact;

use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\SyncMessage\Exception\PluginNotSupportedBySpecificationException;

abstract class AbstractPluginPactDriver extends PactDriver
{
    public function cleanUp(): void
    {
        $this->client->call('pactffi_cleanup_plugins', $this->pactRegistry->getId());
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
        if ($this->getSpecification() < $this->client->get('PactSpecification_V4')) {
            throw new PluginNotSupportedBySpecificationException($this->config->getPactSpecificationVersion());
        }

        $this->client->call('pactffi_using_plugin', $this->pactRegistry->getId(), $this->getPluginName(), $this->getPluginVersion());

        return $this;
    }
}
