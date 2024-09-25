<?php

namespace PhpPact\Consumer\Driver\Body;

use PhpPact\Consumer\Driver\Exception\MessageContentsNotAddedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\Message;
use PhpPact\FFI\ClientInterface;

class MessageBodyDriver implements MessageBodyDriverInterface
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function registerBody(Message $message): void
    {
        $body = $message->getContents();
        $partId = $this->client->getInteractionPartRequest();
        switch (true) {
            case $body instanceof Binary:
                $success = $this->client->withBinaryFile($message->getHandle(), $partId, $body->getContentType(), $body->getData());
                break;

            case $body instanceof Text:
                $success = $this->client->withBody($message->getHandle(), $partId, $body->getContentType(), $body->getContents());
                break;

            default:
                $success = true;
                break;
        };
        if (!$success) {
            throw new MessageContentsNotAddedException();
        }
    }
}
