<?php

namespace XmlConsumer\Service;

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

    public function getMovies(): string
    {
        $response = $this->httpClient->get(new Uri("{$this->baseUri}/movies"), [
            'headers' => ['Accept' => 'application/xml']
        ]);

        return $response->getBody();
    }
}
