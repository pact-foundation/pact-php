<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\Message;

interface SharedMessageDriverInterface extends DriverInterface
{
    public function registerMessage(Message $message): void;
}
