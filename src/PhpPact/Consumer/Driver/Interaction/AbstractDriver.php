<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Exception\InteractionCommentNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionKeyNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionPendingNotSetException;
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
        $success = $this->client->setKey($interaction->getHandle(), $key);
        if (!$success) {
            throw new InteractionKeyNotSetException(sprintf("Can not set the key '%s' for the interaction '%s'", $key, $interaction->getDescription()));
        }
    }

    protected function setPending(Interaction|Message $interaction): void
    {
        $pending = $interaction->getPending();
        if (null === $pending) {
            return;
        }
        $success = $this->client->setPending($interaction->getHandle(), $pending);
        if (!$success) {
            throw new InteractionPendingNotSetException(sprintf("Can not mark interaction '%s' as pending", $interaction->getDescription()));
        }
    }

    protected function setComments(Interaction|Message $interaction): void
    {
        foreach ($interaction->getComments() as $key => $value) {
            $value = (is_string($value) || is_null($value)) ? $value : json_encode($value);
            if (is_bool($value)) {
                throw new InteractionCommentNotSetException(sprintf("Can not json encode value of comment '%s'", $key));
            }
            $success = $this->client->setComment($interaction->getHandle(), $key, $value);
            if (!$success) {
                throw new InteractionCommentNotSetException(sprintf("Can not add comment '%s' to the interaction '%s'", $key, $interaction->getDescription()));
            }
        }
        foreach ($interaction->getTextComments() as $value) {
            $success = $this->client->addTextComment($interaction->getHandle(), $value);
            if (!$success) {
                throw new InteractionCommentNotSetException(sprintf("Can not add text comment '%s' to the interaction '%s'", $value, $interaction->getDescription()));
            }
        }
    }
}
