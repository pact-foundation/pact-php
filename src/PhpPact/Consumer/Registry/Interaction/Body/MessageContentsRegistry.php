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
        switch (true) {
            case $body instanceof Binary:
                $data = $body->getData();
                $success = $this->client->call('pactffi_with_binary_file', $this->messageRegistry->getId(), $this->getPart(), $body->getContentType(), $data->getValue(), $data->getSize());
                break;

            case $body instanceof Text:
                $success = $this->client->call('pactffi_with_body', $this->messageRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents());
                break;

            case $body instanceof Multipart:
                throw new BodyNotSupportedException('Message does not support multipart');
        };
        if (!isset($success) || !$success) {
            throw new MessageContentsNotAddedException();
        }
    }
}
