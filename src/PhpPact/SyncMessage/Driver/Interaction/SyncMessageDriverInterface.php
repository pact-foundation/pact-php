<?php

namespace PhpPact\SyncMessage\Driver\Interaction;

use PhpPact\Consumer\Driver\Interaction\SharedMessageDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\MockService\Model\VerifyResult;

interface SyncMessageDriverInterface extends SharedMessageDriverInterface
{
    public function verifyMessage(): VerifyResult;

    public function registerMessage(Message $message): void;
}
