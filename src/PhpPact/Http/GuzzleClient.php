<?php

namespace PhpPact\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Guzzle Client Interface Wrapper
 */
class GuzzleClient implements ClientInterface
{
    private Client $client;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->client = new Client($config);
    }

    /**
     * @param array<string, mixed> $options
     * @throws GuzzleException
     */
    public function get(UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->client->get($uri, $options);
    }

    /**
     * @param array<string, mixed> $options
     * @throws GuzzleException
     */
    public function put(UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->client->put($uri, $options);
    }

    /**
     * @param array<string, mixed> $options
     * @throws GuzzleException
     */
    public function delete(UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->client->delete($uri, $options);
    }

    /**
     * @param array<string, mixed> $options
     * @throws GuzzleException
     */
    public function post(UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->client->post($uri, $options);
    }
}
