<?php

namespace PhpPact\Consumer\Registry\Interaction\Body;

use FFI;
use FFI\CData;
use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\Consumer\Exception\PartNotAddedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
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

    public function withBody(Text|Binary|Multipart $body): void
    {
        switch (true) {
            case $body instanceof Binary:
                $data = $body->getData();
                $success = $this->client->call('pactffi_with_binary_file', $this->interactionRegistry->getId(), $this->getPart(), $body->getContentType(), $data->getValue(), $data->getSize());
                break;

            case $body instanceof Text:
                $success = $this->client->call('pactffi_with_body', $this->interactionRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents());
                break;

            case $body instanceof Multipart:
                foreach ($body->getParts() as $part) {
                    $result = $this->client->call('pactffi_with_multipart_file_v2', $this->interactionRegistry->getId(), $this->getPart(), $part->getContentType(), $part->getPath(), $part->getName(), $body->getBoundary());
                    if ($result->failed instanceof CData) {
                        throw new PartNotAddedException(FFI::string($result->failed));
                    }
                }
                $success = true;
                break;
        };
        if (!isset($success) || !$success) {
            throw new InteractionBodyNotAddedException();
        }
    }

    abstract protected function getPart(): int;
}
