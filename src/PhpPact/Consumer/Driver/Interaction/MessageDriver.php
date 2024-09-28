<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\Message;

class MessageDriver extends AbstractMessageDriver implements MessageDriverInterface
{
    public function reify(Message $message): string
    {
        return $this->client->messageReify($message->getHandle());
    }
}
