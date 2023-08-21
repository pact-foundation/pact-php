<?php

namespace PhpPact\Consumer\Registry\Interaction\Body;

use PhpPact\Consumer\Exception\BodyNotSupportedException;
use PhpPact\Consumer\Exception\MessageContentsNotAddedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
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

    public function withBody(Text|Binary|Multipart $body): void
    {
        $success = match ($body::class) {
            Binary::class => $this->client->call('pactffi_with_binary_file', $this->messageRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents()->getValue(), $body->getContents()->getSize()),
            Text::class => $this->client->call('pactffi_with_body', $this->messageRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents()),
            Multipart::class => throw new BodyNotSupportedException('Message does not support multipart'),
            default => false,
        };
        if (!$success) {
            throw new MessageContentsNotAddedException();
        }
    }
}
