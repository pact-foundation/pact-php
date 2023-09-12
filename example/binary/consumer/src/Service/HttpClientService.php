<?php

namespace BinaryConsumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

/**
 * Example HTTP Service
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

    public function getImageContent(): string
    {
        $response = $this->httpClient->get(new Uri("{$this->baseUri}/image.jpg"), [
            'headers' => ['Accept' => 'image/jpeg']
        ]);

        return $response->getBody();
    }
}
