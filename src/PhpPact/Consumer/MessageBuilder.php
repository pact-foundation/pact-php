<?php

namespace PhpPact\Consumer;

use Exception;
use PhpPact\Ffi\Helper;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

/**
 * Build a message and send it to the Pact Rust FFI
 * Class MessageBuilder.
 */
class MessageBuilder extends AbstractBuilder
{
    protected array $callback;
    protected int $messagePact;
    protected int $message;

    /**
     * MessageBuilder constructor.
     *
     * {@inheritdoc}
     */
    public function __construct(MockServerConfigInterface $config)
    {
        parent::__construct($config);
        $this->messagePact = $this->ffi->pactffi_new_message_pact($config->getConsumer(), $config->getProvider());
        $this->message     = $this->ffi->pactffi_new_message($this->messagePact, '');
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
     * @param string $description what is given to the request
     * @param array  $params      for that request
     *
     * @return MessageBuilder
     */
    public function given(string $description, array $params = []): self
    {
        if (\count($params) > 0) {
            foreach ($params as $name => $value) {
                $this->ffi->pactffi_message_given_with_param($this->message, $description, (string) $name, $value);
            }
        } else {
            $this->ffi->pactffi_message_given($this->message, $description);
        }

        return $this;
    }

    /**
     * @param string $description what is received when the request is made
     *
     * @return MessageBuilder
     */
    public function expectsToReceive(string $description): self
    {
        $this->ffi->pactffi_message_expects_to_receive($this->message, $description);

        return $this;
    }

    /**
     * @param array $metadata what is the additional metadata of the message
     *
     * @return MessageBuilder
     */
    public function withMetadata(array $metadata): self
    {
        foreach ($metadata as $key => $value) {
            $this->ffi->pactffi_message_with_metadata($this->message, $key, $value);
        }

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
        if (\is_string($contents)) {
            $contentType = 'text/plain';
        } else {
            $contents    = \json_encode($contents);
            $contentType = 'application/json';
        }

        $contents = Helper::getString($contents);
        $this->ffi->pactffi_message_with_contents($this->message, $contentType, $contents->getValue(), $contents->getSize());

        return $this;
    }

    /**
     * Run reify to create an example pact from the message (i.e. create messages from matchers)
     *
     * @return string
     */
    public function reify(): string
    {
        return $this->ffi->pactffi_message_reify($this->message);
    }

    /**
     * Wrapper around verify()
     *
     * @param callable     $callback
     * @param false|string $description description of the pact and thus callback
     *
     * @throws Exception
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
     * @throws Exception if callback is not set
     *
     * @return bool
     */
    public function verify(): bool
    {
        if (\count($this->callback) < 1) {
            throw new \Exception('Callbacks need to exist to run verify.');
        }

        $contents = $this->reify();

        // call the function to actually run the logic
        try {
            foreach ($this->callback as $callback) {
                //@todo .. what do with the providerState
                \call_user_func($callback, $contents);
            }

            return !$this->ffi->pactffi_write_message_pact_file($this->messagePact, $this->config->getPactDir(), true);
        } catch (\Exception $e) {
            return false;
        }
    }
}
