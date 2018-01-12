<?php

namespace PhpPact\Core\Http;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Guzzle Client Interface Wrapper
 * Class GuzzleClient
 */
class GuzzleClient implements ClientInterface
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function get(UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->client->get($uri, $options);
    }

    public function put(UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->client->put($uri, $options);
    }

    public function delete(UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->client->delete($uri, $options);
    }

    public function post(UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->client->post($uri, $options);
    }
}
