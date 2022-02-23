<?php

namespace PhpPact\Broker\Service;

use PhpPact\Http\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Http Service Wrapper for Pact Broker
 * Class BrokerHttpService.
 */
class BrokerHttpClient implements BrokerHttpClientInterface
{
    /** @var ClientInterface */
    private $httpClient;

    /** @var UriInterface */
    private $baseUri;

    /** @var array */
    private $headers;

    /**
     * {@inheritdoc}
     */
    public function __construct(ClientInterface $httpClient, UriInterface $baseUri, array $headers = [])
    {
        $this->httpClient = $httpClient;
        $this->baseUri    = $baseUri;
        $this->headers    = $headers;

        if (!\array_key_exists('Content-Type', $headers)) {
            $this->headers['Content-Type'] = 'application/json';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function publishJson(string $version, string $json)
    {
        $array    = \json_decode($json, true);
        $consumer = $array['consumer']['name'];
        $provider = $array['provider']['name'];

        /** @var UriInterface $uri */
        $uri = $this->baseUri->withPath("/pacts/provider/{$provider}/consumer/{$consumer}/version/{$version}");

        $this->httpClient->put($uri, [
            'headers' => $this->headers,
            'body'    => $json,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function tag(string $consumer, string $version, string $tag)
    {
        /** @var UriInterface $uri */
        $uri = $this->baseUri->withPath("/pacticipants/{$consumer}/versions/{$version}/tags/{$tag}");
        $this->httpClient->put($uri, [
            'headers' => $this->headers,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllConsumerUrls(string $provider, string $version = 'latest'): array
    {
        if ($version !== 'latest') {
            @\trigger_error(\sprintf('The second argument "version" in "%s()" method makes no sense and will be removed in any upcoming major version', __METHOD__), E_USER_DEPRECATED);
        }

        $uri = $this->baseUri->withPath("/pacts/provider/{$provider}/latest");

        $response = $this->httpClient->get($uri, [
            'headers' => $this->headers,
        ]);

        $json = \json_decode($response->getBody()->getContents(), true);

        $urls = [];
        foreach ($json['_links']['pacts'] as $pact) {
            $urls[] = $pact['href'];
        }

        return $urls;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllConsumerUrlsForTag(string $provider, string $tag): array
    {
        $uri = $this->baseUri->withPath("/pacts/provider/{$provider}/latest/{$tag}");

        $response = $this->httpClient->get($uri, [
            'headers' => $this->headers,
        ]);

        $json = \json_decode($response->getBody()->getContents(), true);

        $urls = [];
        foreach ($json['_links']['pacts'] as $pact) {
            $urls[] = $pact['href'];
        }

        return $urls;
    }
}
