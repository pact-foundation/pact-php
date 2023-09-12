<?php

namespace PhpPact\Consumer\Registry\Interaction\Body;

use FFI;
use FFI\CData;
use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\Consumer\Exception\PartNotAddedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Part;
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
        $success = match ($body::class) {
            Binary::class => $this->client->call('pactffi_with_binary_file', $this->interactionRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents()->getValue(), $body->getContents()->getSize()),
            Text::class => $this->client->call('pactffi_with_body', $this->interactionRegistry->getId(), $this->getPart(), $body->getContentType(), $body->getContents()),
            Multipart::class => array_reduce(
                $body->getParts(),
                function (bool $success, Part $part) {
                    $result = $this->client->call('pactffi_with_multipart_file', $this->interactionRegistry->getId(), $this->getPart(), $part->getContentType(), $part->getPath(), $part->getName());
                    if ($result->failed instanceof CData) {
                        throw new PartNotAddedException(FFI::string($result->failed));
                    }

                    return true;
                },
                true
            ),
            default => false,
        };
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }

    abstract protected function getPart(): int;
}
