<?php

namespace GraphqlConsumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class HttpClientService
{
    private Client $httpClient;

    public function __construct(private string $baseUri)
    {
        $this->httpClient = new Client();
    }

    public function query(): string
    {
        $response = $this->httpClient->post(new Uri("{$this->baseUri}/api"), [
            'json' => [
                'query' => <<<GRAPHQL
                query(\$message: String!) {
                    echo(message: \$message)
                }
                GRAPHQL,
                'variables' => [
                    'message' => 'Hi everyone!',
                ],
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
                mutation(\$x: Int!, \$y: Int!) {
                    sum(
                        x: \$x,
                        y: \$y
                    )
                }
                GRAPHQL,
                'variables' => [
                    'x' => 3,
                    'y' => 5,
                ],
            ],
            'headers' => ['Content-Type' => 'application/json']
        ]);

        return $response->getBody();
    }
}
