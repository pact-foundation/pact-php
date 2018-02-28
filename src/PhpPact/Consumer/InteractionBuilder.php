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

    /** @var MockServerHttpService */
    private $mockServerHttpService;

    /**
     * InteractionBuilder constructor.
     *
     * @param MockServerConfigInterface $config
     */
    public function __construct(MockServerConfigInterface $config)
    {
        $this->interaction           = new Interaction();
        $this->mockServerHttpService = new MockServerHttpService(new GuzzleClient(), $config);
        $this->config                = $config;
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

        return $this->mockServerHttpService->registerInteraction($this->interaction);
    }

    /**
     * @inheritdoc
     */
    public function verify(): bool
    {
        return $this->mockServerHttpService->verifyInteractions();
    }

    /**
     * @inheritdoc
     */
    public function finalize(): bool
    {
        // Write the pact file to disk.
        $this->mockServerHttpService->getPactJson();

        // Delete the interactions.
        $this->mockServerHttpService->deleteAllInteractions();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function writePact(): bool
    {
        // Write the pact file to disk.
        $this->mockServerHttpService->getPactJson();

        return true;
    }
}
