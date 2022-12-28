<?php

namespace PhpPact\Standalone\PactMessage;

use PhpPact\Consumer\Model\AbstractPact;
use PhpPact\Consumer\Model\Message;

class PactMessage extends AbstractPact
{
    /**
     * @param Message $message
     *
     * @return string
     */
    public function reify(Message $message): string
    {
        $message->setId($this->newInteraction($message->getDescription()));
        $this
            ->given($message)
            ->expectsToReceive($message)
            ->withMetadata($message)
            ->withContent($message);

        return $this->ffi->pactffi_message_reify($message->getId());
    }

    /**
     * Update a pact with the given message, or create the pact if it does not exist.
     *
     * @return bool
     */
    public function update(): bool
    {
        $this->writePact();
        $this->cleanUp();

        return true;
    }

    private function given(Message $message): self
    {
        foreach ($message->getProviderStates() as $providerState) {
            foreach ($providerState->params as $key => $value) {
                $this->ffi->pactffi_message_given_with_param($message->getId(), $providerState->name, (string) $key, $value);
            }
        }

        return $this;
    }

    private function expectsToReceive(Message $message): self
    {
        $this->ffi->pactffi_message_expects_to_receive($message->getId(), $message->getDescription());

        return $this;
    }

    private function withMetadata(Message $message): self
    {
        foreach ($message->getMetadata() as $key => $value) {
            $this->ffi->pactffi_message_with_metadata($message->getId(), (string) $key, (string) $value);
        }

        return $this;
    }

    private function withContent(Message $message): self
    {
        if (\is_string($message->getContents())) {
            $contents    = $message->getContents();
            $contentType = 'text/plain';
        } else {
            $contents    = \json_encode($message->getContents());
            $contentType = 'application/json';
        }

        $this->withBody($message->getId(), $this->ffi->InteractionPart_Request, $contentType, $contents);

        return $this;
    }

    protected function newInteraction(?string $description): int
    {
        return $this->ffi->pactffi_new_message_interaction($this->id, $description);
    }
}
