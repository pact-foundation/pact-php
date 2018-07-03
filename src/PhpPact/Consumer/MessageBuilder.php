<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Standalone\PactMessage\PactMessage;

/**
 * Build a message and send it to the Ruby Standalone Mock Service
 * Class MessageBuilder.
 */
class MessageBuilder implements BuilderInterface
{
    /** @var Message */
    private $message;

    /** @var PactMessage */
    protected $pactMessage;


    /**
     * constructor.
     *
     */
    public function __construct()
    {
        $this->message = new Message();
        $this->pactMessage = new PactMessage();
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
    public function withContent($contents): self
    {
        $this->message->setContents($contents);
        return $this;
    }

    /**
     * Run reify to create an example pact from the message (i.e. create messages from matchers)
     *
     * @return string
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     */
    public function reify(): string
    {
        return $this->pactMessage->reify($this->message);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(): bool
    {

        //return $this->mockServerHttpService->verifyInteractions();

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(): bool
    {
        // Write the pact file to disk.
        //$this->mockServerHttpService->getPactJson();

        // Delete the interactions.
        //$this->mockServerHttpService->deleteAllInteractions();

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function writePact(): bool
    {
        // Write the pact file to disk.
        //$this->mockServerHttpService->getPactJson();

        return false;
    }
}
