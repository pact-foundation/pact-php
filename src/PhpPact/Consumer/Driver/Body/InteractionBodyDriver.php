<?php

namespace PhpPact\Consumer\Driver\Body;

use FFI;
use FFI\CData;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\Consumer\Exception\PartNotAddedException;
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
            InteractionPart::REQUEST => $this->client->get('InteractionPart_Request'),
            InteractionPart::RESPONSE => $this->client->get('InteractionPart_Response'),
        };
        switch (true) {
            case $body instanceof Binary:
                $data = $body->getData();
                $success = $this->client->call('pactffi_with_binary_file', $interaction->getHandle(), $partId, $body->getContentType(), $data->getValue(), $data->getSize());
                break;

            case $body instanceof Text:
                $success = $this->client->call('pactffi_with_body', $interaction->getHandle(), $partId, $body->getContentType(), $body->getContents());
                break;

            case $body instanceof Multipart:
                foreach ($body->getParts() as $part) {
                    $result = $this->client->call('pactffi_with_multipart_file_v2', $interaction->getHandle(), $partId, $part->getContentType(), $part->getPath(), $part->getName(), $body->getBoundary());
                    if ($result->failed instanceof CData) {
                        throw new PartNotAddedException(FFI::string($result->failed));
                    }
                }
                $success = true;
                break;

            default:
                break;
        };
        if (isset($success) && false === $success) {
            throw new InteractionBodyNotAddedException();
        }
    }
}
