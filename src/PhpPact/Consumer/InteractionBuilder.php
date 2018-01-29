<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Standalone\MockService\Service\MockServerHttpService;

/**
 * Build an interaction and send it to the Ruby Standalone Mock Service
 * Class InteractionBuilder
 */
class InteractionBuilder implements InteractionBuilderInterface
{
    /** @var Interaction */
    private $interaction;

    /** @var MockServerConfigInterface */
    private $config;

    /**
     * InteractionBuilder constructor.
     *
     * @param MockServerConfigInterface $config
     */
    public function __construct(MockServerConfigInterface $config)
    {
        $this->interaction = new Interaction();
        $this->config      = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function given(string $description): InteractionBuilderInterface
    {
        $this->interaction->setDescription($description);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function uponReceiving(string $providerState): InteractionBuilderInterface
    {
        $this->interaction->setProviderState($providerState);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function with(ConsumerRequest $request): InteractionBuilderInterface
    {
        $this->interaction->setRequest($request);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function willRespondWith(ProviderResponse $response): bool
    {
        $this->interaction->setResponse($response);

        return $this->send();
    }

    /**
     * {@inheritdoc}
     */
    private function send(): bool
    {
        $service = new MockServerHttpService(new GuzzleClient(), $this->config);

        return $service->registerInteraction($this->interaction);
    }
}
