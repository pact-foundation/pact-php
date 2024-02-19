<?php

namespace PhpPact\Consumer\Driver\Body;

use PhpPact\Consumer\Exception\MessageContentsNotAddedException;
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
        $partId = $this->client->get('InteractionPart_Request');
        switch (true) {
            case $body instanceof Binary:
                $data = $body->getData();
                $success = $this->client->call('pactffi_with_binary_file', $message->getHandle(), $partId, $body->getContentType(), $data->getValue(), $data->getSize());
                break;

            case $body instanceof Text:
                $success = $this->client->call('pactffi_with_body', $message->getHandle(), $partId, $body->getContentType(), $body->getContents());
                break;

            default:
                break;
        };
        if (isset($success) && false === $success) {
            throw new MessageContentsNotAddedException();
        }
    }
}
