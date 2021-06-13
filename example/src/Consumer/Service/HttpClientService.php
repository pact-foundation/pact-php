<?php

namespace Consumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

/**
 * Example HTTP Service
 * Class HttpClientService
 */
class HttpClientService
{
    private Client $httpClient;
    private string $baseUri;

    public function __construct(string $baseUri)
    {
        $this->httpClient = new Client();
        $this->baseUri    = $baseUri;
    }

    /**
     * Get Hello String
     *
     * @param string $name
     *
     * @return string
     */
    public function getHelloString(string $name): string
    {
        $response = $this->httpClient->get(new Uri("{$this->baseUri}/hello/{$name}"), [
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $body   = $response->getBody();
        $object = \json_decode($body);

        return $object->message->data->generate;
    }

    /**
     * Get Goodbye String
     *
     * @param string $name
     *
     * @return string
     */
    public function getGoodbyeString(string $name): string
    {
        $response = $this->httpClient->get("{$this->baseUri}/goodbye/{$name}", [
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $body   = $response->getBody();
        $object = \json_decode($body);

        return $object->message;
    }
}
