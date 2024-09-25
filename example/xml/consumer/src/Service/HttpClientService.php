<?php

namespace XmlConsumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class HttpClientService
{
    private Client $httpClient;

    public function __construct(private string $baseUri)
    {
        $this->httpClient = new Client();
    }

    public function getMovies(): string
    {
        $response = $this->httpClient->get(new Uri("{$this->baseUri}/movies"), [
            'headers' => ['Accept' => 'application/xml']
        ]);

        return $response->getBody();
    }
}
