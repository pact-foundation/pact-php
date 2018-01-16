<?php

namespace PhpPact\Consumer;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;

/**
 * Interface InteractionBuilderInterface
 */
interface InteractionBuilderInterface
{
    /**
     * @param string $description what is given to the request
     *
     * @return InteractionBuilderInterface
     */
    public function given(string $description): self;

    /**
     * @param string $providerState what is received when the request is made
     *
     * @return InteractionBuilderInterface
     */
    public function uponReceiving(string $providerState): self;

    /**
     * @param ConsumerRequest $request mock of request sent
     *
     * @return InteractionBuilderInterface
     */
    public function with(ConsumerRequest $request): self;

    /**
     * Make the http request to the Mock Service to register the interaction.
     *
     * @param ProviderResponse $response mock of response received
     *
     * @return bool returns true on success
     */
    public function willRespondWith(ProviderResponse $response): bool;
}
