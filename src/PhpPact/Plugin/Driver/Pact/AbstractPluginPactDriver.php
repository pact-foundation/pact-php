<?php

namespace PhpPact\Plugin\Driver\Pact;

use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\Plugin\Exception\PluginNotSupportedBySpecificationException;

abstract class AbstractPluginPactDriver extends PactDriver
{
    public function deletePact(Pact $pact): void
    {
        $this->client->call('pactffi_cleanup_plugins', $pact->handle);
        parent::deletePact($pact);
    }

    public function newPact(): Pact
    {
        $pact = parent::newPact();
        $this->usingPlugin($pact);

        return $pact;
    }

    abstract protected function getPluginName(): string;

    protected function getPluginVersion(): ?string
    {
        return null;
    }

    private function usingPlugin(Pact $pact): void
    {
        if ($this->getSpecification() < $this->client->get('PactSpecification_V4')) {
            throw new PluginNotSupportedBySpecificationException($this->config->getPactSpecificationVersion());
        }

        $this->client->call('pactffi_using_plugin', $pact->handle, $this->getPluginName(), $this->getPluginVersion());
    }
}
