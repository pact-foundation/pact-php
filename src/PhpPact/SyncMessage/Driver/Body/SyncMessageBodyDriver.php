<?php

namespace PhpPact\SyncMessage\Driver\Body;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\FFI\ClientInterface;
use PhpPact\SyncMessage\Model\SyncMessage;

class SyncMessageBodyDriver implements SyncMessageBodyDriverInterface
{
    public function __construct(private readonly ClientInterface $client)
    {
    }

    public function registerBody(SyncMessage $message): void
    {
        $body = $message->getRequestContents();
        $partId = $this->client->getInteractionPartRequest();
        switch (true) {
            case $body instanceof Binary:
                $this->client->withBinaryFile($message->getHandle(), $partId, $body->getContentType(), $body->getData());
                break;

            case $body instanceof Text:
                $this->client->withBody($message->getHandle(), $partId, $body->getContentType(), $body->getContents());
                break;

            default:
                break;
        };
        $bodyList = $message->getResponseContentsList();
        $partId = $this->client->getInteractionPartResponse();
        foreach ($bodyList as $body) {
            if ($body instanceof Binary) {
                $this->client->withBinaryFile($message->getHandle(), $partId, $body->getContentType(), $body->getData());
            }

            if ($body instanceof Text) {
                $this->client->withBody($message->getHandle(), $partId, $body->getContentType(), $body->getContents());
            }
        }
    }
}
