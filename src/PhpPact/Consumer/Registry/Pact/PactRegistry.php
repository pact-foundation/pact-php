<?php

namespace PhpPact\Consumer\Registry\Pact;

use PhpPact\Consumer\Exception\PactNotRegisteredException;
use PhpPact\FFI\ClientInterface;

class PactRegistry implements PactRegistryInterface
{
    protected int $id;

    public function __construct(protected ClientInterface $client)
    {
    }

    public function getId(): int
    {
        if (!isset($this->id)) {
            throw new PactNotRegisteredException('New pact must be registered.');
        }
        return $this->id;
    }

    public function deletePact(): void
    {
        $this->client->call('pactffi_free_pact_handle', $this->id);
        unset($this->id);
    }

    public function registerPact(string $consumer, string $provider, int $specification): void
    {
        $this
            ->newPact($consumer, $provider)
            ->withSpecification($specification);
    }

    private function newPact(string $consumer, string $provider): self
    {
        $this->id = $this->client->call('pactffi_new_pact', $consumer, $provider);

        return $this;
    }

    private function withSpecification(int $specification): self
    {
        $this->client->call('pactffi_with_specification', $this->id, $specification);

        return $this;
    }
}
