<?php

namespace PhpPact\SyncMessage\Driver\Interaction;

use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\MockService\Model\VerifyResult;

interface SyncMessageDriverInterface
{
    public function verifyMessage(): VerifyResult;

    public function registerMessage(Message $message): void;
}
