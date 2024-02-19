<?php

namespace PhpPact\SyncMessage\Driver\Interaction;

use PhpPact\Consumer\Driver\Interaction\DriverInterface;
use PhpPact\Consumer\Model\Message;

interface SyncMessageDriverInterface extends DriverInterface
{
    public function verifyMessage(): bool;

    public function registerMessage(Message $message): void;
}
