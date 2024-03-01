<?php

namespace PhpPact\SyncMessage\Driver\Interaction;

use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Registry\Interaction\MessageRegistryInterface;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\Standalone\MockService\Model\VerifyResult;

class SyncMessageDriver implements SyncMessageDriverInterface
{
    public function __construct(
        private MessageRegistryInterface $messageRegistry,
        private MockServerInterface $mockServer
    ) {
    }

    public function verifyMessage(): VerifyResult
    {
        return $this->mockServer->verify();
    }

    public function registerMessage(Message $message): void
    {
        $this->messageRegistry->registerMessage($message);

        $this->mockServer->start();
    }
}
