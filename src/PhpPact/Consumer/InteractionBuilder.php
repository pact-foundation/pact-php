<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Factory\InteractionDriverFactory;
use PhpPact\Consumer\Factory\InteractionDriverFactoryInterface;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

/**
 * Build an interaction and send it to the Ruby Standalone Mock Service
 */
class InteractionBuilder implements BuilderInterface
{
    private InteractionDriverInterface $driver;
    private Interaction $interaction;

    public function __construct(MockServerConfigInterface $config, ?InteractionDriverFactoryInterface $driverFactory = null)
    {
        $this->driver      = ($driverFactory ?? new InteractionDriverFactory())->create($config);
        $this->interaction = new Interaction();
    }

    /**
     * @param string $providerState what is given to the request
     * @param array<string, string>  $params    for that request
     * @param bool   $overwrite clear pass states completely and start this array
     */
    public function given(string $providerState, array $params = [], bool $overwrite = false): self
    {
        $this->interaction->setProviderState($providerState, $params, $overwrite);

        return $this;
    }

    /**
     * @param string $description what is received when the request is made
     */
    public function uponReceiving(string $description): self
    {
        $this->interaction->setDescription($description);

        return $this;
    }

    /**
     * @param ConsumerRequest $request mock of request sent
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
        return $this->driver->verifyInteractions()->matched;
    }
}
