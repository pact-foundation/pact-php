<?php

namespace CsvConsumer\Service;

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

    public function getReport(): array
    {
        $response = $this->httpClient->get(new Uri("{$this->baseUri}/report.csv"), [
            'headers' => ['Accept' => 'text/csv']
        ]);

        return str_getcsv($response->getBody());
    }
}
