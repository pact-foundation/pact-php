<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Driver\Interaction\MessageDriverInterface;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Exception\MissingCallbackException;
use PhpPact\Consumer\Factory\MessageDriverFactory;
use PhpPact\Consumer\Factory\MessageDriverFactoryInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Consumer\Model\Message;

/**
 * Build a message and send it to the Ruby Standalone Mock Service
 */
class MessageBuilder implements BuilderInterface
{
    protected MessageDriverInterface $driver;
    protected Message $message;

    /**
     * @var array<mixed, callable>
     */
    protected array $callback = [];

    public function __construct(PactConfigInterface $config, ?MessageDriverFactoryInterface $driverFactory = null)
    {
        $this->message = new Message();
        $this->driver = ($driverFactory ?? new MessageDriverFactory())->create($config);
    }

    /**
     * @param array<string, mixed> $params
     */
    public function given(string $name, array $params = [], bool $overwrite = false): self
    {
        $this->message->setProviderState($name, $params, $overwrite);

        return $this;
    }

    public function expectsToReceive(string $description): self
    {
        $this->message->setDescription($description);

        return $this;
    }

    /**
     * @param array<string, MatcherInterface|string> $metadata
     */
    public function withMetadata(array $metadata): self
    {
        $this->message->setMetadata($metadata);

        return $this;
    }

    public function withContent(mixed $contents): self
    {
        $this->message->setContents($contents);

        return $this;
    }

    public function key(?string $key): self
    {
        $this->message->setKey($key);

        return $this;
    }

    public function pending(?bool $pending): self
    {
        $this->message->setPending($pending);

        return $this;
    }

    /**
     * @param array<string, mixed> $comments
     */
    public function comments(array $comments): self
    {
        $this->message->setComments($comments);

        return $this;
    }

    public function comment(string $comment): self
    {
        $this->message->addTextComment($comment);

        return $this;
    }

    /**
     * Retrieve the verification call back
     *
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
     * Run reify to create an example pact from the message (i.e. create messages from matchers)
     */
    public function reify(): string
    {
        $this->driver->registerMessage($this->message);

        return $this->driver->reify($this->message);
    }

    /**
     * Wrapper around verify()
     *
     * @param null|string $description description of the pact and thus callback
     *
     * @throws MissingCallbackException
     */
    public function verifyMessage(callable $callback, ?string $description = null): bool
    {
        $this->setCallback($callback, $description);

        return $this->verify();
    }

    /**
     * Verify the use of the pact by calling the callback
     * It also calls finalize to write the pact
     *
     * @throws MissingCallbackException if callback is not set
     */
    public function verify(): bool
    {
        if (\count($this->callback) < 1) {
            throw new MissingCallbackException('Callbacks need to exist to run verify.');
        }

        $pactJson = $this->reify();

        // call the function to actually run the logic
        try {
            foreach ($this->callback as $callback) {
                //@todo .. what do with the providerState
                \call_user_func($callback, $pactJson);
            }

            $this->driver->writePactAndCleanUp();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
