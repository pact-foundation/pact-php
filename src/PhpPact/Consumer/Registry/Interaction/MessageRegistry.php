<?php

namespace PhpPact\Consumer\Registry\Interaction;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\ProviderState;
use PhpPact\Consumer\Registry\Interaction\Body\BodyRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\Body\MessageContentsRegistry;
use PhpPact\Consumer\Registry\Pact\PactRegistryInterface;
use PhpPact\FFI\ClientInterface;

class MessageRegistry extends AbstractRegistry implements MessageRegistryInterface
{
    private BodyRegistryInterface $messageContentsRegistry;

    public function __construct(
        ClientInterface $client,
        PactRegistryInterface $pactRegistry,
        ?BodyRegistryInterface $messageContentsRegistry = null
    ) {
        parent::__construct($client, $pactRegistry);
        $this->messageContentsRegistry = $messageContentsRegistry ?? new MessageContentsRegistry($client, $this);
    }


    public function registerMessage(Message $message): void
    {
        $this
            ->newInteraction($message->getDescription())
            ->given($message->getProviderStates())
            ->expectsToReceive($message->getDescription())
            ->withMetadata($message->getMetadata())
            ->withContents($message->getContents());
    }

    protected function newInteraction(string $description): self
    {
        $this->id = $this->client->call('pactffi_new_message_interaction', $this->pactRegistry->getId(), $description);

        return $this;
    }

    private function withContents(Text|Binary|null $contents): self
    {
        if ($contents) {
            $this->messageContentsRegistry->withBody($contents);
        }

        return $this;
    }

    private function expectsToReceive(string $description): self
    {
        $this->client->call('pactffi_message_expects_to_receive', $this->id, $description);

        return $this;
    }

    /**
     * @param ProviderState[] $providerStates
     */
    private function given(array $providerStates): self
    {
        foreach ($providerStates as $providerState) {
            $this->client->call('pactffi_message_given', $this->id, $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->client->call('pactffi_message_given_with_param', $this->id, $providerState->getName(), (string) $key, (string) $value);
            }
        }

        return $this;
    }

    /**
     * @param array<string, string> $metadata
     */
    private function withMetadata(array $metadata): self
    {
        foreach ($metadata as $key => $value) {
            $this->client->call('pactffi_message_with_metadata', $this->id, (string) $key, (string) $value);
        }

        return $this;
    }
}
