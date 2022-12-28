<?php

namespace PhpPact\Consumer\Driver;

use PhpPact\Consumer\Model\Message;

class MessageDriver extends AbstractMessageDriver implements MessageDriverInterface
{
    /**
     * @param Message $message
     *
     * @return string
     */
    public function reify(Message $message): string
    {
        $this->registerMessage($message);

        return $this->ffi->pactffi_message_reify($this->interactionId);
    }

    /**
     * Update a pact with the given message, or create the pact if it does not exist.
     *
     * @return bool
     */
    public function update(): bool
    {
        $this->writePact();
        $this->cleanUp();

        return true;
    }

    protected function newInteraction(string $description): self
    {
        $this->interactionId = $this->ffi->pactffi_new_message_interaction($this->pactId, $description);

        return $this;
    }
}
