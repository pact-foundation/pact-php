<?php

namespace PhpPact\SyncMessage\Registry\Interaction;

use PhpPact\Consumer\Model\ProviderState;
use PhpPact\Consumer\Registry\Interaction\MessageRegistry;

class SyncMessageRegistry extends MessageRegistry
{
    protected function newInteraction(string $description): self
    {
        $this->id = $this->client->call('pactffi_new_sync_message_interaction', $this->pactRegistry->getId(), $description);

        return $this;
    }

    /**
     * @param ProviderState[] $providerStates
     */
    protected function given(array $providerStates): self
    {
        foreach ($providerStates as $providerState) {
            $this->client->call('pactffi_given', $this->id, $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->client->call('pactffi_given_with_param', $this->id, $providerState->getName(), (string) $key, (string) $value);
            }
        }

        return $this;
    }
}
