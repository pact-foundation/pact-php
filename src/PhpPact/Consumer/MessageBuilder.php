<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

/**
 * Build a message and send it to the Ruby Standalone Mock Service
 * Class MessageBuilder.
 */
class MessageBuilder extends PactBuilder
{
    /** @var Message */
    private $message;


    /**
     * InteractionBuilder constructor.
     *
     * @param MockServerConfigInterface $config
     */
    public function __construct(MockServerConfigInterface $config)
    {
        $this->message = new Message();
        parent::__construct($config);
    }

    /**
     * @param string $providerState what is given to the request
     *
     * @return MessageBuilder
     */
    public function given(string $providerState): self
    {
        $this->message->setProviderState($providerState);

        return $this;
    }

    /**
     * @param string $description what is received when the request is made
     *
     * @return MessageBuilder
     */
    public function expectsToReceive(string $description): self
    {
        $this->message->setDescription($description);

        return $this;
    }



    /**
     * @param mixed $metadata what is the additional metadata of the message
     *
     * @return MessageBuilder
     */
    public function withMetadata($metadata): self
    {
        $this->message->setMetadata($metadata);

        return $this;
    }

    /**
     * Make the http request to the Mock Service to register the message.  Content is required.
     *
     * @param mixed $contents required to be in the message
     *
     * @return bool returns true on success
     */
    public function withContent($contents): bool
    {
        $this->message->setContents($contents);

        return $this->mockServerHttpService->registerMessage($this->message);
    }

}
