<?php

namespace StubServerConsumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class HttpClientService
{
    private Client $httpClient;

    public function __construct(private string $baseUri)
    {
        $this->httpClient = new Client();
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getResults(): array
    {
        $response = $this->httpClient->get(new Uri("{$this->baseUri}/test"), [
            'http_errors' => false,
        ]);
        $body   = $response->getBody();
        /** @var object{results: array<array-key, mixed>} $object */
        $object = \json_decode($body, null, 512, JSON_THROW_ON_ERROR);

        return $object->results;
    }
}
