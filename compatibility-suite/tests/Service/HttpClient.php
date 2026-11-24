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
        $headers = [];
        foreach ($request->getHeaders() as $name => $values) {
            if ([] !== $values) {
                $headers[$name] = $values;
            }
        }

        $options = [
            'query' => $this->formatQueryString($request->getQuery()),
            'headers' => $headers,
            'http_errors' => false,
        ];

        $body = $request->getBody();
        if ($body instanceof Text || $body instanceof Binary) {
            $options['body'] = $body instanceof Text
                ? $body->getContents()
                : (string) file_get_contents($body->getPath());
            $options['headers']['Content-Type'] = [$body->getContentType()];
        }

        return $this->client->request($request->getMethod(), $uri->withPath($request->getPath()), $options);
    }

    /**
     * @param array<string, string[]|null> $query
     */
    private function formatQueryString(array $query): string
    {
        $result = [];

        foreach ($query as $key => $values) {
            foreach ((array) $values as $value) {
                $result[] = urlencode((string) $key) . '=' . urlencode((string) $value);
            }
        }

        return implode('&', $result);
    }
}
