<?php

namespace PhpPact\Consumer\Driver;

use PhpPact\Consumer\Model\Interaction;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

class InteractionDriver extends AbstractDriver implements InteractionDriverInterface
{
    use HasMockServerTrait;

    public function __construct(MockServerConfigInterface $config)
    {
        parent::__construct($config);
    }

    protected function getMockServerTransport(): string
    {
        return $this->config->isSecure() ? 'https' : 'http';
    }

    protected function getMockServerConfig(): MockServerConfigInterface
    {
        return $this->config;
    }

    public function verifyInteractions(): bool
    {
        return $this->mockServerMatched();
    }

    protected function cleanUp(): void
    {
        $this->cleanUpMockServer();
        parent::cleanUp();
    }

    protected function writePact(): void
    {
        $this->mockServerWritePact();
    }

    public function registerInteraction(Interaction $interaction): bool
    {
        $this
            ->newInteraction($interaction->getDescription())
            ->given($interaction)
            ->uponReceiving($interaction)
            ->with($interaction)
            ->willRespondWith($interaction)
            ->createMockServer();

        return true;
    }

    private function given(Interaction $interaction): self
    {
        $this->setProviderStates($interaction->getProviderStates());

        return $this;
    }

    private function uponReceiving(Interaction $interaction): self
    {
        $this->setDescription($interaction->getDescription());

        return $this;
    }

    private function with(Interaction $interaction): self
    {
        $request = $interaction->getRequest();
        $this->ffi->pactffi_with_request($this->interactionId, $request->getMethod(), $request->getPath());
        $this->withHeaders($this->ffi->InteractionPart_Request, $request->getHeaders());
        $this->withQuery($request->getQuery());
        $this->withBody($this->ffi->InteractionPart_Request, null, $request->getBody());

        return $this;
    }

    private function willRespondWith(Interaction $interaction): self
    {
        $response = $interaction->getResponse();
        $this->ffi->pactffi_response_status($this->interactionId, $response->getStatus());
        $this->withHeaders($this->ffi->InteractionPart_Response, $response->getHeaders());
        $this->withBody($this->ffi->InteractionPart_Response, null, $response->getBody());

        return $this;
    }

    private function withHeaders(int $part, array $headers): void
    {
        foreach ($headers as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->pactffi_with_header_v2($this->interactionId, $part, (string) $header, (int) $index, (string) $value);
            }
        }
    }

    private function withQuery(array $query): void
    {
        foreach ($query as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->pactffi_with_query_parameter_v2($this->interactionId, (string) $key, (int) $index, (string) $value);
            }
        }
    }
}
