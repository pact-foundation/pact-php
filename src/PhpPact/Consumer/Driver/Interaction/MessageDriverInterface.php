<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\Message;

interface MessageDriverInterface extends SharedMessageDriverInterface
{
    public function reify(Message $message): string;
}
