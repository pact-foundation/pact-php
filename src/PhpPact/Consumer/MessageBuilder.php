<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\PactConfigInterface;
use PhpPact\Standalone\PactMessage\PactMessage;

/**
 * Build a message and send it to the Ruby Standalone Mock Service
 */
class MessageBuilder implements BuilderInterface
{
    protected PactMessage $pactMessage;

    protected PactConfigInterface $config;

    /**
     * @var array<mixed, callable>
     */
    protected array $callback;

    private Message $message;

    public function __construct(PactConfigInterface $config)
    {
        $this->config      = $config;
        $this->message     = new Message();
        $this->pactMessage = new PactMessage();
    }

    /**
     * Retrieve the verification call back
     *
     * @param callable    $callback
     * @param null|string $description of the callback in case of multiple
     */
    public function setCallback(callable $callback, ?string $description = null): self
    {
        if ($description !== null) {
            $this->callback[$description] = $callback;
        } else {
            $this->callback[0] = $callback;
        }

        return $this;
    }

    /**
     * @param string $name      what is given to the request
     * @param array<mixed, mixed>  $params    for that request
     * @param bool   $overwrite clear pass states completely and start this array
     *
     * @return MessageBuilder
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
     *
     * @return self
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
     */
    public function reify(): string
    {
        return $this->pactMessage->reify($this->message);
    }

    /**
     * Wrapper around verify()
     *
     * @param callable     $callback
     * @param null|string $description description of the pact and thus callback
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function verifyMessage(callable $callback, ?string $description = null): bool
    {
        $this->setCallback($callback, $description);

        return $this->verify($description);
    }

    /**
     * Verify the use of the pact by calling the callback
     * It also calls finalize to write the pact
     *
     * @param null|string $description description of the pact and thus callback
     *
     * @throws \Exception if callback is not set
     *
     * @return bool
     */
    public function verify(?string $description = null): bool
    {
        if (\count($this->callback) < 1) {
            throw new \Exception('Callbacks need to exist to run verify.');
        }

        $pactJson = $this->reify();

        // call the function to actually run the logic
        try {
            foreach ($this->callback as $callback) {
                //@todo .. what do with the providerState
                \call_user_func($callback, $pactJson);
            }

            return $this->writePact();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Write the Pact without deleting the interactions.
     *
     * @return bool
     */
    public function writePact(): bool
    {
        // you do not want to save the reified json
        $pactJson = \json_encode($this->message, JSON_THROW_ON_ERROR);

        return $this->pactMessage->update($pactJson, $this->config->getConsumer(), $this->config->getProvider(), $this->config->getPactDir());
    }
}
