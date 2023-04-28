<?php

namespace PhpPact\Broker\Service;

use PhpPact\Http\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Interface for http interaction with the PACT Broker.
 */
interface BrokerHttpClientInterface
{
    /**
     * @param ClientInterface       $client  Http Client
     * @param UriInterface          $baseUri Base URI for the PhpPact Broker
     * @param array<string, string> $headers additional headers
     */
    public function __construct(ClientInterface $client, UriInterface $baseUri, array $headers);

    /**
     * Publish JSON.
     *
     * @param string $version Consumer version
     * @param string $json    PACT File JSON
     */
    public function publishJson(string $version, string $json): void;

    /**
     * Tag a consumer version with a tag.
     */
    public function tag(string $consumer, string $version, string $tag): void;

    /**
     * Get all Pact urls for the consumer.
     *
     * @param string $provider provider name
     * @param string $version  version of the provider
     *
     * @return array<int, string>
     */
    public function getAllConsumerUrls(string $provider, string $version = 'latest'): array;

    /**
     * Get all Pact URLs for a specific tag.
     *
     * @return array<int, string>
     */
    public function getAllConsumerUrlsForTag(string $provider, string $tag): array;
}
