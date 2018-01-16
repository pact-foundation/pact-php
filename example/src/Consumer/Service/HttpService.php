<?php

namespace Consumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class HttpService
{
    /** @var Client */
    private $httpClient;

    /** @var string */
    private $baseUri;

    public function __construct(string $baseUri)
    {
        $this->httpClient = new Client();
        $this->baseUri    = $baseUri;
    }

    public function getHelloString(string $name): string
    {
        $response = $this->httpClient->request('GET', new Uri("{$this->baseUri}/hello/{$name}"), [
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $body   = $response->getBody();
        $object = \json_decode($body);

        return $object->message;
    }

    public function getGoodbyeString(string $name): string
    {
        $response = $this->httpClient->request('GET', "{$this->baseUri}/goodbye/{$name}", [
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $body   = $response->getBody();
        $object = \json_decode($body);

        return $object->message;
    }
}
