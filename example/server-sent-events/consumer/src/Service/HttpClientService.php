<?php

namespace ServerSentEventsConsumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\StreamInterface;

class HttpClientService
{
    private Client $httpClient;

    public function __construct(private string $baseUri)
    {
        $this->httpClient = new Client();
    }

    public function getEvents(): StreamInterface
    {
        $response = $this->httpClient->get(new Uri("{$this->baseUri}/events"), [
            'headers' => [
                'Accept' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Last-Event-ID' => 123,
            ],
            'stream' => true,
            'http_errors' => false,
        ]);

        return $response->getBody();
    }
}
