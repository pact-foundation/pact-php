<?php

namespace PhpPact\Consumer\Registry\Interaction\Body;

use PhpPact\Consumer\Exception\MessageContentsNotAddedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Registry\Interaction\MessageRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\Part\RequestPartTrait;
use PhpPact\FFI\ClientInterface;

class MessageContentsRegistry implements BodyRegistryInterface
{
    use RequestPartTrait;

    public function __construct(
        protected ClientInterface $client,
        protected MessageRegistryInterface $messageRegistry
    ) {
    }

    public function withBody(Text|Binary $body): void
    {
        if ($body instanceof Binary) {
            $success = $this->client->call('pactffi_with_binary_file', $this->messageRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents()->getValue(), $body->getContents()->getSize());
        } else {
            $success = $this->client->call('pactffi_with_body', $this->messageRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents());
        }
        if (!$success) {
            throw new MessageContentsNotAddedException();
        }
    }
}
