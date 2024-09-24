<?php

namespace GraphqlConsumer\Service;

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

    public function query(): string
    {
        $response = $this->httpClient->post(new Uri("{$this->baseUri}/api"), [
            'json' => [
                'query' => <<<GRAPHQL
                query {
                    echo(message: "Hello World")
                }
                GRAPHQL,
            ],
            'headers' => ['Content-Type' => 'application/json']
        ]);

        return $response->getBody();
    }

    public function mutation(): string
    {
        $response = $this->httpClient->post(new Uri("{$this->baseUri}/api"), [
            'json' => [
                'query' => <<<GRAPHQL
                mutation { sum(x: 2, y: 2) }
                GRAPHQL,
            ],
            'headers' => ['Content-Type' => 'application/json']
        ]);

        return $response->getBody();
    }
}
