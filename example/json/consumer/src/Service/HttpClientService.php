<?php

namespace JsonConsumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

/**
 * Example HTTP Service
 */
class HttpClientService
{
    private Client $httpClient;

    public function __construct(private string $baseUri)
    {
        $this->httpClient = new Client();
    }

    /**
     * Get Hello String
     */
    public function getHelloString(string $name): string
    {
        $response = $this->httpClient->get(new Uri("{$this->baseUri}/hello/{$name}"), [
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $body   = $response->getBody();
        $object = \json_decode($body, null, 512, JSON_THROW_ON_ERROR);

        return $object->message;
    }

    /**
     * Get Goodbye String
     */
    public function getGoodbyeString(string $name): string
    {
        $response = $this->httpClient->get("{$this->baseUri}/goodbye/{$name}", [
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $body   = $response->getBody();
        $object = \json_decode($body, null, 512, JSON_THROW_ON_ERROR);

        return $object->message;
    }
}
