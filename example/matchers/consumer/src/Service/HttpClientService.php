<?php

namespace MatchersConsumer\Service;

use GuzzleHttp\Client;

class HttpClientService
{
    private Client $httpClient;

    private string $baseUri;

    public function __construct(string $baseUri)
    {
        $this->httpClient = new Client();
        $this->baseUri    = $baseUri;
    }

    public function getMatchers(): array
    {
        $response = $this->httpClient->get("{$this->baseUri}/matchers", [
            'headers' => ['Accept' => 'application/json'],
            'query' => 'ignore=statusCode&pages=2&pages=3&locales[]=fr-BE&locales[]=ru-RU',
        ]);

        return \json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
