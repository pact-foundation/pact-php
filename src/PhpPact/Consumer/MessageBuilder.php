<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Driver\MessageDriver;
use PhpPact\Consumer\Driver\MessageDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Config\PactConfigInterface;

/**
 * Build a message and send it to the Rust FFI Mock Service
 * Class MessageBuilder.
 */
class MessageBuilder implements BuilderInterface
{
    /** @var MessageDriverInterface */
    protected MessageDriverInterface $driver;

    /** @var array callable */
    protected array $callback;

    /** @var Message */
    protected Message $message;

    /**
     * @param PactConfigInterface $config
     */
    public function __construct(PactConfigInterface $config)
    {
        $this->message     = new Message();
        $this->driver      = new MessageDriver($config);
    }

    /**
     * Retrieve the verification call back
     *
     * @param callable     $callback
     * @param false|string $description of the callback in case of multiple
     *
     * @return MessageBuilder
     */
    public function setCallback(callable $callback, $description = false): self
    {
        if ($description) {
            $this->callback[$description] = $callback;
        } else {
            $this->callback[0] = $callback;
        }

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
        $this->message->setProviderState($name, $params, $overwrite);

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
     * @param array $metadata what is the additional metadata of the message
     *
     * @return MessageBuilder
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
    public function withContent(mixed $contents): self
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
        return $this->driver->reify($this->message);
    }

    /**
     * Wrapper around verify()
     *
     * @param callable     $callback
     * @param false|string $description description of the pact and thus callback
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function verifyMessage(callable $callback, $description = false): bool
    {
        $this->setCallback($callback, $description);

        return $this->verify();
    }

    /**
     * Verify the use of the pact by calling the callback
     * It also calls finalize to write the pact
     *
     * @throws \Exception if callback is not set
     *
     * @return bool
     */
    public function verify(): bool
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
        return $this->driver->update();
    }
}
