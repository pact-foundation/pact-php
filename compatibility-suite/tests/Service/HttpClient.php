<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class HttpClient implements HttpClientInterface
{
    private ClientInterface $client;

    public function __construct(
    ) {
        $this->client = new Client();
    }

    public function sendRequest(ConsumerRequest $request, UriInterface $uri): ResponseInterface
    {
        $options = [];
        $options['query'] = $this->formatQueryString($request->getQuery());
        $options['headers'] = $request->getHeaders();
        $body = $request->getBody();
        if ($body instanceof Text || $body instanceof Binary) {
            $options['body'] = match (true) {
                $body instanceof Text => $body->getContents(),
                $body instanceof Binary => file_get_contents($body->getPath()),
            };
            $options['headers']['Content-Type'] = $body->getContentType();
        }
        $options['http_errors'] = false;

        return $this->client->request($request->getMethod(), $uri->withPath($request->getPath()), $options);
    }

    private function formatQueryString(array $query): string
    {
        $result = [];

        foreach ($query as $key => $values) {
            foreach ($values as $value) {
                $result[] = urlencode($key) . '=' . urlencode($value);
            }
        }

        return implode('&', $result);
    }
}
