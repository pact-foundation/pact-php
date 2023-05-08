<?php

namespace PhpPact\Consumer\Driver\Interaction;

class InteractionDriver extends AbstractDriver implements InteractionDriverInterface
{
    public function newInteraction(string $description): void
    {
        $this->id = $this->client->call('pactffi_new_interaction', $this->pactDriver->getId(), $description);
    }

    public function uponReceiving(string $description): void
    {
        $this->client->call('pactffi_upon_receiving', $this->id, $description);
    }

    public function given(array $providerStates): void
    {
        foreach ($providerStates as $providerState) {
            $this->client->call('pactffi_given', $this->id, $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->client->call('pactffi_given_with_param', $this->id, $providerState->getName(), (string) $key, (string) $value);
            }
        }
    }
}
