<?php

namespace PhpPact\Consumer\Driver;

use PhpPact\Consumer\Model\Message;

abstract class AbstractMessageDriver extends AbstractDriver
{
    protected function registerMessage(Message $message): void
    {
        $this
            ->newInteraction($message->getDescription())
            ->given($message)
            ->expectsToReceive($message)
            ->withMetadata($message)
            ->withContent($message);
    }

    private function given(Message $message): self
    {
        $this->setProviderStates($message->getProviderStates());

        return $this;
    }

    private function expectsToReceive(Message $message): self
    {
        $this->setDescription($message->getDescription());

        return $this;
    }

    private function withMetadata(Message $message): self
    {
        foreach ($message->getMetadata() as $key => $value) {
            $this->ffi->pactffi_message_with_metadata($this->interactionId, (string) $key, (string) $value);
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

        $this->withBody($this->ffi->InteractionPart_Request, $contentType, $contents);

        return $this;
    }
}
