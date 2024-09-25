<?php

namespace GeneratorsConsumer\Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class HttpClientService
{
    private Client $httpClient;

    public function __construct(private string $baseUri)
    {
        $this->httpClient = new Client();
    }

    public function sendRequest(): ResponseInterface
    {
        return $this->httpClient->get("{$this->baseUri}/generators", [
            'headers' => ['Accept' => 'application/json'],
            'json' => ['id' => 112],
            'http_errors' => false,
        ]);
    }
}
