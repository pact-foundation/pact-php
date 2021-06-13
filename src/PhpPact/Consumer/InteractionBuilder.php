<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

/**
 * Build an interaction and send it to the Pact Rust FFI
 * Class InteractionBuilder.
 */
class InteractionBuilder extends AbstractBuilder
{
    protected int $pact;
    protected int $interaction;
    protected ?int $port = null;

    /**
     * InteractionBuilder constructor.
     *
     * {@inheritdoc}
     */
    public function __construct(MockServerConfigInterface $config)
    {
        parent::__construct($config);
        $this->pact = $this->ffi->pactffi_new_pact($config->getConsumer(), $config->getProvider());
        $this->ffi->pactffi_with_specification($this->pact, $this->getPactSpecificationVersion());
    }

    /**
     * @param string $description what is received when the request is made
     *
     * @return InteractionBuilder
     */
    public function newInteraction(string $description = ''): self
    {
        $this->interaction = $this->ffi->pactffi_new_interaction($this->pact, $description);

        return $this;
    }

    /**
     * @param string $providerState what is given to the request
     *
     * @return InteractionBuilder
     */
    public function given(string $providerState): self
    {
        $this->ffi->pactffi_given($this->interaction, $providerState);

        return $this;
    }

    /**
     * @param string $description what is received when the request is made
     *
     * @return InteractionBuilder
     */
    public function uponReceiving(string $description): self
    {
        $this->ffi->pactffi_upon_receiving($this->interaction, $description);

        return $this;
    }

    /**
     * @param ConsumerRequest $request mock of request sent
     *
     * @return InteractionBuilder
     */
    public function with(ConsumerRequest $request): self
    {
        $this->ffi->pactffi_with_request($this->interaction, $request->getMethod(), $request->getPath());
        foreach ($request->getHeaders() as $header => $values) {
            foreach ($values as $index => $value) {
                $this->ffi->pactffi_with_header($this->interaction, $this->ffi->InteractionPart_Request, $header, $index, $value);
            }
        }
        foreach ($request->getQuery() as $key => $values) {
            $values = \is_array($values) ? $values : [$values];
            foreach ($values as $index => $value) {
                $this->ffi->pactffi_with_query_parameter($this->interaction, $key, $index, $value);
            }
        }
        if (!\is_null($request->getBody())) {
            $this->ffi->pactffi_with_body($this->interaction, $this->ffi->InteractionPart_Request, null, $request->getBody());
        }

        return $this;
    }

    /**
     * @param ProviderResponse $response mock of response received
     *
     * @return InteractionBuilder
     */
    public function willRespondWith(ProviderResponse $response): self
    {
        $this->ffi->pactffi_response_status($this->interaction, $response->getStatus());
        foreach ($response->getHeaders() as $header => $values) {
            foreach ($values as $index => $value) {
                $this->ffi->pactffi_with_header($this->interaction, $this->ffi->InteractionPart_Response, $header, $index, $value);
            }
        }
        if (!\is_null($response->getBody())) {
            $this->ffi->pactffi_with_body($this->interaction, $this->ffi->InteractionPart_Response, null, $response->getBody());
        }

        return $this;
    }

    public function createMockServer(): void
    {
        $this->port = $this->ffi->pactffi_create_mock_server_for_pact($this->pact, '127.0.0.1:0', false);
    }

    /**
     * {@inheritdoc}
     *
     * @throws MockServerNotStartedException
     */
    public function verify(): bool
    {
        if ($this->port === null) {
            throw new MockServerNotStartedException('Mock server is not started.');
        }

        $result = $this->ffi->pactffi_mock_server_matched($this->port);

        if ($result) {
            $this->ffi->pactffi_write_pact_file($this->port, $this->config->getPactDir(), true);
        }

        $this->ffi->pactffi_cleanup_mock_server($this->port);
        $this->port = null;

        return $result;
    }

    /**
     * @throws MockServerNotStartedException
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        if ($this->port === null) {
            throw new MockServerNotStartedException('Mock server is not started.');
        }

        return \sprintf('http://localhost:%d', $this->port);
    }
}
