<?php

namespace PhpPact\Consumer\Registry\Interaction\Body;

use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistryInterface;
use PhpPact\FFI\ClientInterface;

abstract class AbstractBodyRegistry implements BodyRegistryInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected InteractionRegistryInterface $interactionRegistry
    ) {
    }

    public function withBody(Text|Binary $body): void
    {
        if ($body instanceof Binary) {
            $success = $this->client->call('pactffi_with_binary_file', $this->interactionRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents()->getValue(), $body->getContents()->getSize());
        } else {
            $success = $this->client->call('pactffi_with_body', $this->interactionRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents());
        }
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }

    abstract protected function getPart(): int;
}
