<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\PactConfigInterface;
use PhpPact\Standalone\PactMessage\PactMessage;

/**
 * Build a message and send it to the Ruby Standalone Mock Service
 * Class MessageBuilder.
 */
class MessageBuilder implements BuilderInterface
{
    /** @var PactMessage */
    protected $pactMessage;

    /** @var PactConfigInterface */
    protected $config;

    /** @var callable */
    protected $callback;

    /** @var Message */
    private $message;

    /**
     * constructor.
     */
    public function __construct(PactConfigInterface $config)
    {
        $this->config      = $config;
        $this->message     = new Message();
        $this->pactMessage = new PactMessage();
    }

    /**
     * Retrieve the verification call back
     *
     * @param callable $callback
     *
     * @return MessageBuilder
     */
    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @param string $name      what is given to the request
     * @param array  $params    for that request
     * @param bool   $overwrite clear pass states completely and start this array
     *
     * @return MessageBuilder
     */
    public function given(string $name, array $params = [], $overwrite = false): self
    {
        $this->message->addProviderState($name, $params);

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
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     *
     * @return string
     */
    public function reify(): string
    {
        return $this->pactMessage->reify($this->message);
    }

    /**
     * Wrapper around verify()
     *
     * @param callable $callback
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function verifyMessage($callback): bool
    {
        $this->setCallback($callback);

        return $this->verify();
    }

    /**
     * Verify the use of the pact by calling the callback
     * It also calls finalize to write the pact
     *
     * @throws \Exception if callback is not set
     */
    public function verify(): bool
    {
        if (!$this->callback) {
            throw new \Exception('Callbacks need to exist to run verify.');
        }

        $pactJson = $this->reify();

        // call the function to actually run the logic
        try {
            \call_user_func($this->callback, $pactJson);

            return $this->writePact();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Write the Pact without deleting the interactions.
     *
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     *
     * @return bool
     */
    public function writePact(): bool
    {
        // you do not want to save the reified json
        $pactJson = \json_encode($this->message);

        return $this->pactMessage->update($pactJson, $this->config->getConsumer(), $this->config->getProvider(), $this->config->getPactDir());
    }
}
