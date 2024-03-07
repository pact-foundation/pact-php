<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Driver\Interaction\MessageDriverInterface;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Exception\MissingCallbackException;
use PhpPact\Consumer\Factory\MessageDriverFactory;
use PhpPact\Consumer\Factory\MessageDriverFactoryInterface;

/**
 * Build a message and send it to the Ruby Standalone Mock Service
 */
class MessageBuilder extends AbstractMessageBuilder
{
    protected MessageDriverInterface $driver;

    /**
     * @var array<mixed, callable>
     */
    protected array $callback = [];

    public function __construct(PactConfigInterface $config, ?MessageDriverFactoryInterface $driverFactory = null)
    {
        parent::__construct();
        $this->driver = ($driverFactory ?? new MessageDriverFactory())->create($config);
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
