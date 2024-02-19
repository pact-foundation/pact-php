<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\Message;

interface MessageDriverInterface extends DriverInterface
{
    public function registerMessage(Message $message): void;

    public function reify(Message $message): string;
}
