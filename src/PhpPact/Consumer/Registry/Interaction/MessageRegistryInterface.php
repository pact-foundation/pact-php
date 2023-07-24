<?php

namespace PhpPact\Consumer\Registry\Interaction;

use PhpPact\Consumer\Model\Message;

interface MessageRegistryInterface extends RegistryInterface
{
    public function registerMessage(Message $message): void;
}
