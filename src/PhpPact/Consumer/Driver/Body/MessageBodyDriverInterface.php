<?php

namespace PhpPact\Consumer\Driver\Body;

use PhpPact\Consumer\Model\Message;

interface MessageBodyDriverInterface
{
    public function registerBody(Message $message): void;
}
