<?php

namespace PhpPact\Plugin\Driver\Body;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Exception\BodyNotSupportedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\Message;
use PhpPact\FFI\ClientInterface;
use PhpPact\Plugin\Exception\PluginBodyNotAddedException;

class PluginBodyDriver implements PluginBodyDriverInterface
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function registerBody(Interaction|Message $interaction, InteractionPart $interactionPart): void
    {
        $body = $interaction instanceof Message ? $interaction->getContents() : $interaction->getBody($interactionPart);
        $partId = $interaction instanceof Message ? $this->client->getInteractionPartRequest() : match ($interactionPart) {
            InteractionPart::REQUEST => $this->client->getInteractionPartRequest(),
            InteractionPart::RESPONSE => $this->client->getInteractionPartResponse(),
        };
        switch (true) {
            case $body instanceof Binary:
                throw new BodyNotSupportedException('Plugin does not support binary body');

            case $body instanceof Text:
                json_decode($body->getContents());
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new BodyNotSupportedException('Plugin only support json body contents');
                }
                $error = $this->client->interactionContents($interaction->getHandle(), $partId, $body->getContentType(), $body->getContents());
                if ($error) {
                    throw new PluginBodyNotAddedException($error);
                }
                break;

            case $body instanceof Multipart:
                throw new BodyNotSupportedException('Plugin does not support multipart body');

            default:
                break;
        };
    }
}
