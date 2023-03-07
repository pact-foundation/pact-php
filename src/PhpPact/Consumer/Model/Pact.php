<?php

namespace PhpPact\Consumer\Model;

use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

/**
 * Class Pact.
 */
class Pact extends AbstractPact
{
    /**
     * @param MockServerConfigInterface $config
     */
    public function __construct(MockServerConfigInterface $config)
    {
        parent::__construct($config);
        $this->initWithLogLevel();
    }

    private function createMockServer(): void
    {
        $port = $this->ffi->pactffi_create_mock_server_for_transport(
            $this->id,
            $this->config->getHost(),
            $this->config->getPort(),
            $this->config->isSecure() ? 'https' : 'http',
            null
        );

        if ($port < 0) {
            throw new MockServerNotStartedException($port);
        }
        $this->config->setPort($port);
    }

    public function verifyInteractions(): bool
    {
        $matched = $this->ffi->pactffi_mock_server_matched($this->config->getPort());

        try {
            if ($matched) {
                $this->writePact();
            }
        } finally {
            $this->cleanUp();
        }

        return $matched;
    }

    protected function cleanUp(): void
    {
        parent::cleanUp();
        $this->ffi->pactffi_cleanup_mock_server($this->config->getPort());
    }

    public function registerInteraction(Interaction $interaction): bool
    {
        $interaction->setId($this->newInteraction($interaction->getDescription()));
        $this
            ->given($interaction)
            ->uponReceiving($interaction)
            ->with($interaction)
            ->willRespondWith($interaction)
            ->createMockServer();

        return true;
    }

    private function initWithLogLevel(): self
    {
        $logLevel = $this->config->getLogLevel();
        if ($logLevel) {
            $this->ffi->pactffi_init_with_log_level($logLevel);
        }

        return $this;
    }

    private function given(Interaction $interaction): self
    {
        foreach ($interaction->getProviderStates() as $providerState) {
            foreach ($providerState->getParams() as $key => $value) {
                $this->ffi->pactffi_given_with_param($interaction->getId(), $providerState->getName(), $key, $value);
            }
        }

        return $this;
    }

    private function uponReceiving(Interaction $interaction): self
    {
        $this->ffi->pactffi_upon_receiving($interaction->getId(), $interaction->getDescription());

        return $this;
    }

    private function with(Interaction $interaction): self
    {
        $id = $interaction->getId();
        $request = $interaction->getRequest();
        $this->ffi->pactffi_with_request($id, $request->getMethod(), $request->getPath());
        $this->withHeaders($id, $this->ffi->InteractionPart_Request, $request->getHeaders());
        $this->withQuery($id, $request->getQuery());
        $this->withBody($id, $this->ffi->InteractionPart_Request, null, $request->getBody());

        return $this;
    }

    private function willRespondWith(Interaction $interaction): self
    {
        $id = $interaction->getId();
        $response = $interaction->getResponse();
        $this->ffi->pactffi_response_status($id, $response->getStatus());
        $this->withHeaders($id, $this->ffi->InteractionPart_Response, $response->getHeaders());
        $this->withBody($id, $this->ffi->InteractionPart_Response, null, $response->getBody());

        return $this;
    }

    private function withHeaders(int $interaction, int $part, array $headers): void
    {
        foreach ($headers as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->pactffi_with_header_v2($interaction, $part, (string) $header, (int) $index, (string) $value);
            }
        }
    }

    private function withQuery(int $interaction, array $query): void
    {
        foreach ($query as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->pactffi_with_query_parameter_v2($interaction, (string) $key, (int) $index, (string) $value);
            }
        }
    }
}
