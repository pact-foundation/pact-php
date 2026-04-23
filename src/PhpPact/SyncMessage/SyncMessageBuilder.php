<?php

namespace PhpPact\SyncMessage;

use PhpPact\Consumer\BuilderInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactory;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactoryInterface;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\SyncMessage\Model\SyncMessage;

class SyncMessageBuilder implements BuilderInterface
{
    protected SyncMessage $message;
    private SyncMessageDriverInterface $driver;

    public function __construct(MockServerConfigInterface $config, ?SyncMessageDriverFactoryInterface $driverFactory = null)
    {
        $this->driver = ($driverFactory ?? new SyncMessageDriverFactory())->create($config);
        $this->message = new SyncMessage();
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

    public function withRequestContents(mixed $requestContents): self
    {
        $this->message->setRequestContents($requestContents);

        return $this;
    }

    /**
     * @param array<array<string, mixed>> $contentsList
     */
    public function withResponseContentsList(array $contentsList): self
    {
        $this->message->setResponseContentsList($contentsList);

        return $this;
    }

    /**
     * @param array<string, mixed> $responseContents
     */
    public function withResponseContents(array $responseContents): self
    {
        $this->message->addResponseContents($responseContents);

        return $this;
    }

    public function withRequestMatchingRules(string $requestMatchingRules): self
    {
        $this->message->setRequestMatchingRules($requestMatchingRules);

        return $this;
    }

    public function withResponseMatchingRules(string $responseMatchingRules): self
    {
        $this->message->setResponseMatchingRules($responseMatchingRules);

        return $this;
    }

    public function withRequestGenerators(string $requestGenerators): self
    {
        $this->message->setRequestGenerators($requestGenerators);

        return $this;
    }

    public function withResponseGenerators(string $responseGenerators): self
    {
        $this->message->setResponseGenerators($responseGenerators);

        return $this;
    }

    public function registerMessage(): void
    {
        $this->driver->registerMessage($this->message);
    }

    public function verify(): bool
    {
        return $this->driver->verifyMessage()->matched;
    }
}
