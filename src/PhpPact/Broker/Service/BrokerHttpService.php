<?php

namespace PhpPact\Broker\Service;

use PhpPact\Http\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Http Service Wrapper for Pact Broker
 * Class BrokerHttpService
 */
class BrokerHttpService implements BrokerHttpServiceInterface
{
    /** @var ClientInterface */
    private $httpClient;

    /** @var UriInterface */
    private $baseUri;

    /**
     * @inheritdoc
     */
    public function __construct(ClientInterface $httpClient, UriInterface $baseUri)
    {
        $this->httpClient = $httpClient;
        $this->baseUri    = $baseUri;
    }

    /**
     * @inheritDoc
     */
    public function publishJson(string $json, string $version)
    {
        $array    = \json_decode($json, true);
        $consumer = $array['consumer']['name'];
        $provider = $array['provider']['name'];

        /** @var UriInterface $uri */
        $uri = $this->baseUri->withPath("pacts/provider/{$provider}/consumer/{$consumer}/version/{$version}");

        $this->httpClient->put($uri, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $json
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getAllConsumerUrls(string $provider, string $version = 'latest'): array
    {
        $uri = $this->baseUri->withPath("pacts/provider/{$provider}/latest");

        $response = $this->httpClient->get($uri, [
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $json = \json_decode($response->getBody()->getContents(), true);

        $urls = [];
        foreach ($json['_links']['pacts'] as $pact) {
            $urls[] = $pact['href'];
        }

        return $urls;
    }
}
