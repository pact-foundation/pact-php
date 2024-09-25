<?php

namespace PhpPact\Consumer\Driver\Body;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Driver\Exception\InteractionBodyNotAddedException;
use PhpPact\Consumer\Driver\Exception\PartNotAddedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\FFI\ClientInterface;

class InteractionBodyDriver implements InteractionBodyDriverInterface
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function registerBody(Interaction $interaction, InteractionPart $interactionPart): void
    {
        $body = $interaction->getBody($interactionPart);
        $partId = match ($interactionPart) {
            InteractionPart::REQUEST => $this->client->getInteractionPartRequest(),
            InteractionPart::RESPONSE => $this->client->getInteractionPartResponse(),
        };
        switch (true) {
            case $body instanceof Binary:
                $success = $this->client->withBinaryFile($interaction->getHandle(), $partId, $body->getContentType(), $body->getData());
                break;

            case $body instanceof Text:
                $success = $this->client->withBody($interaction->getHandle(), $partId, $body->getContentType(), $body->getContents());
                break;

            case $body instanceof Multipart:
                foreach ($body->getParts() as $part) {
                    $result = $this->client->withMultipartFileV2($interaction->getHandle(), $partId, $part->getContentType(), $part->getPath(), $part->getName(), $body->getBoundary());
                    if (!$result->success) {
                        throw new PartNotAddedException(sprintf("Can not add part '%s': %s", $part->getName(), $result->message));
                    }
                }
                $success = true;
                break;

            default:
                $success = true;
                break;
        };
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }
}
