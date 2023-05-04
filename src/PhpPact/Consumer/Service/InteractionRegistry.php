<?php

namespace PhpPact\Consumer\Service;

use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Service\Helper\BodyTrait;
use PhpPact\Consumer\Service\Helper\DescriptionTrait;
use PhpPact\Consumer\Service\Helper\ProviderStatesTrait;

class InteractionRegistry implements InteractionRegistryInterface
{
    use ProviderStatesTrait;
    use DescriptionTrait;
    use BodyTrait;

    public function __construct(private MockServerInterface $mockServer)
    {
        $this->createFFI();
    }

    public function verifyInteractions(): bool
    {
        $matched = $this->mockServer->isMatched();

        try {
            if ($matched) {
                $this->writePact();
            }
        } finally {
            $this->cleanUp();
        }

        return $matched;
    }

    public function registerInteraction(Interaction $interaction): bool
    {
        $pactId = $this->mockServer->init();

        $this
            ->newInteraction($pactId, $interaction->getDescription())
            ->given($interaction)
            ->uponReceiving($interaction)
            ->with($interaction)
            ->willRespondWith($interaction);

        $this->mockServer->start();

        return true;
    }

    private function cleanUp(): void
    {
        $this->mockServer->cleanUp();
    }

    private function writePact(): void
    {
        $this->mockServer->writePact();
    }

    private function newInteraction(int $pactId, string $description): self
    {
        $this->interactionId = $this->ffi->pactffi_new_interaction($pactId, $description);

        return $this;
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
        $this->ffi->pactffi_with_request($this->getId(), $request->getMethod(), $request->getPath());
        $this->setHeaders($this->ffi->InteractionPart_Request, $request->getHeaders());
        $this->setQuery($request->getQuery());
        $this->setBody($this->ffi->InteractionPart_Request, null, $request->getBody());

        return $this;
    }

    private function willRespondWith(Interaction $interaction): self
    {
        $response = $interaction->getResponse();
        $this->ffi->pactffi_response_status($this->getId(), $response->getStatus());
        $this->setHeaders($this->ffi->InteractionPart_Response, $response->getHeaders());
        $this->setBody($this->ffi->InteractionPart_Response, null, $response->getBody());

        return $this;
    }

    /**
     * @param array<string, string[]> $headers
     */
    private function setHeaders(int $part, array $headers): void
    {
        foreach ($headers as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->pactffi_with_header_v2($this->getId(), $part, (string) $header, (int) $index, (string) $value);
            }
        }
    }

    /**
     * @param array<string, string[]> $query
     */
    private function setQuery(array $query): void
    {
        foreach ($query as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->pactffi_with_query_parameter_v2($this->getId(), (string) $key, (int) $index, (string) $value);
            }
        }
    }
}
