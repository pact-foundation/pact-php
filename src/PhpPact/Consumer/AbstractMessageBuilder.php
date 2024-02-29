<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Model\Message;

abstract class AbstractMessageBuilder implements BuilderInterface
{
    protected Message $message;

    public function __construct()
    {
        $this->message = new Message();
    }

    /**
     * @param string                 $name      what is given to the request
     * @param array<string, string>  $params    for that request
     * @param bool                   $overwrite clear pass states completely and start this array
     */
    public function given(string $name, array $params = [], bool $overwrite = false): self
    {
        $this->message->setProviderState($name, $params, $overwrite);

        return $this;
    }

    /**
     * @param string $description what is received when the request is made
     */
    public function expectsToReceive(string $description): self
    {
        $this->message->setDescription($description);

        return $this;
    }

    /**
     * @param array<string, string> $metadata what is the additional metadata of the message
     */
    public function withMetadata(array $metadata): self
    {
        $this->message->setMetadata($metadata);

        return $this;
    }

    /**
     * Make the http request to the Mock Service to register the message.  Content is required.
     *
     * @param mixed $contents required to be in the message
     */
    public function withContent(mixed $contents): self
    {
        $this->message->setContents($contents);

        return $this;
    }

    /**
     * Set key for message interaction. This feature only work with specification v4. It doesn't affect pact file with specification <= v3.
     */
    public function key(?string $key): self
    {
        $this->message->setKey($key);

        return $this;
    }
}
