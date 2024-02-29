<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Exception\InteractionKeyNotSetException;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\Message;
use PhpPact\FFI\ClientInterface;

abstract class AbstractDriver
{
    public function __construct(
        protected ClientInterface $client,
    ) {
    }

    protected function setKey(Interaction|Message $interaction): void
    {
        $key = $interaction->getKey();
        if (null === $key) {
            return;
        }
        $success = $this->client->call('pactffi_set_key', $interaction->getHandle(), $key);
        if (!$success) {
            throw new InteractionKeyNotSetException(sprintf("Can not set the key '%s' for the interaction '%s'", $key, $interaction->getDescription()));
        }
    }
}
