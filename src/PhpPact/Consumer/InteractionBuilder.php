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
 * Class InteractionBuilder.
 */
class InteractionBuilder implements BuilderInterface
{
    /** @var MockServerHttpService */
    protected $mockServerHttpService;

    /** @var MockServerConfigInterface */
    protected $config;
    /** @var Interaction */
    private $interaction;

    /**
     * InteractionBuilder constructor.
     *
     * @param MockServerConfigInterface $config
     */
    public function __construct(MockServerConfigInterface $config)
    {
        $this->config                = $config;
        $this->mockServerHttpService = new MockServerHttpService(new GuzzleClient(), $config);
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

    /**
     * {@inheritdoc}
     */
    public function verify(): bool
    {
        return $this->mockServerHttpService->verifyInteractions();
    }

    /**
     * Writes the file to disk and deletes interactions from mock server.
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
     * {@inheritdoc}
     */
    public function writePact(): bool
    {
        // Write the pact file to disk.
        $this->mockServerHttpService->getPactJson();

        return true;
    }
}
