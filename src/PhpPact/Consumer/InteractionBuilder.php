<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfigInterface;


/**
 * Build an interaction and send it to the Ruby Standalone Mock Service
 * Class InteractionBuilder.
 */
class InteractionBuilder extends PactBuilder
{
    /** @var Interaction */
    private $interaction;

    /**
     * InteractionBuilder constructor.
     *
     * @param MockServerConfigInterface $config
     */
    public function __construct(MockServerConfigInterface $config)
    {
        parent::__construct($config);
        $this->interaction           = new Interaction();
    }

    /**
     * @param string $providerState what is given to the request
     *
     * @return InteractionBuilder
     */
    public function given(string $providerState): self
    {
        $this->interaction->setProviderState($providerState);

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
     * Make the http request to the Mock Service to register the interaction.
     *
     * @param ProviderResponse $response mock of response received
     *
     * @return bool returns true on success
     */
    public function willRespondWith(ProviderResponse $response): bool
    {
        $this->interaction->setResponse($response);

        return $this->mockServerHttpService->registerInteraction($this->interaction);
    }

}
