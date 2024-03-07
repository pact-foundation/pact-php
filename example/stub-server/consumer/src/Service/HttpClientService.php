<?php

namespace StubServerConsumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class HttpClientService
{
    private Client $httpClient;

    private string $baseUri;

    public function __construct(string $baseUri)
    {
        $this->httpClient = new Client();
        $this->baseUri    = $baseUri;
    }

    public function getResults(): array
    {
        $response = $this->httpClient->get(new Uri("{$this->baseUri}/test"));
        $body   = $response->getBody();
        $object = \json_decode($body, null, 512, JSON_THROW_ON_ERROR);

        return $object->results;
    }
}
