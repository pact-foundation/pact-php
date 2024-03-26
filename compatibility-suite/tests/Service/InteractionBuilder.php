<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;

final class InteractionBuilder implements InteractionBuilderInterface
{
    public function __construct(
        private RequestBuilderInterface $requestBuilder,
        private ResponseBuilderInterface $responseBuilder
    ) {
    }

    public function build(array $data): Interaction
    {
        $interaction = new Interaction();
        $interaction->setDescription($data['description'] ?? 'Interaction ' . (int) $data['No']);

        $request = new ConsumerRequest();
        $this->requestBuilder->build($request, array_intersect_key($data, array_flip(['method', 'path', 'query', 'headers', 'body'])));
        $interaction->setRequest($request);

        $response = new ProviderResponse();
        $this->responseBuilder->build($response, array_filter([
            'status' => $data['response'] ?? null,
            'headers' => $data['response headers'] ?? null,
            'body' => $data['response body'] ?? null,
            'content-type' => $data['response content'] ?? null,
        ]));
        $interaction->setResponse($response);

        return $interaction;
    }
}
