<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Driver\InteractionDriver;
use PhpPact\Consumer\Driver\InteractionDriverInterface;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

/**
 * Build an interaction and send it to the Ruby Standalone Mock Service
 * Class InteractionBuilder.
 */
class InteractionBuilder implements BuilderInterface
{
    /** @var Interaction */
    protected Interaction $interaction;

    /** @var InteractionDriverInterface */
    protected InteractionDriverInterface $driver;

    /**
     * InteractionBuilder constructor.
     *
     * @param MockServerConfigInterface $config
     */
    public function __construct(MockServerConfigInterface $config)
    {
        $this->interaction           = new Interaction();
        $this->driver                = new InteractionDriver($config);
    }

    /**
     * @param string $providerState what is given to the request
     * @param array  $params    for that request
     * @param bool   $overwrite clear pass states completely and start this array
     *
     * @return InteractionBuilder
     */
    public function given(string $providerState, array $params = [], bool $overwrite = false): self
    {
        $this->interaction->setProviderState($providerState, $params, $overwrite);

        return $this;
    }

    /**
     * @param string $description what is received when the request is made
     *
     * @return InteractionBuilder
     */
    public function uponReceiving(string $description): self
    {
        $this->interaction->setDescription($description);

        return $this;
    }

    /**
     * @param ConsumerRequest $request mock of request sent
     *
     * @return InteractionBuilder
     */
    public function with(ConsumerRequest $request): self
    {
        $this->interaction->setRequest($request);

        return $this;
    }

    /**
     * @param ProviderResponse $response mock of response received
     *
     * @return bool returns true on success
     */
    public function willRespondWith(ProviderResponse $response): bool
    {
        $this->interaction->setResponse($response);

        return $this->driver->registerInteraction($this->interaction);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(): bool
    {
        return $this->driver->verifyInteractions();
    }
}
