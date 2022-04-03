<?php

namespace PhpPact\Broker\Service;

use PhpPact\Http\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Interface for http interaction with the PACT Broker.
 * Interface BrokerHttpServiceInterface.
 */
interface BrokerHttpClientInterface
{
    /**
     * HttpServiceInterface constructor.
     *
     * @param ClientInterface $client  Http Client
     * @param UriInterface    $baseUri Base URI for the PhpPact Broker
     * @param array           $headers additional headers
     */
    public function __construct(ClientInterface $client, UriInterface $baseUri, array $headers);

    /**
     * Publish JSON.
     *
     * @param string $version Consumer version
     * @param string $json    PACT File JSON
     */
    public function publishJson(string $version, string $json);

    /**
     * Tag a consumer version with a tag.
     *
     * @param string $consumer
     * @param string $version
     * @param string $tag
     *
     * @return mixed
     */
    public function tag(string $consumer, string $version, string $tag);

    /**
     * Get all Pact urls for the consumer.
     *
     * @param string $provider provider name
     * @param string $version  version of the provider
     *
     * @return string[]
     */
    public function getAllConsumerUrls(string $provider, string $version = 'latest'): array;

    /**
     * Get all Pact URLs for a specific tag.
     *
     * @param string $provider
     * @param string $tag
     *
     * @return array
     */
    public function getAllConsumerUrlsForTag(string $provider, string $tag): array;

    /**
     * Publish contract
     *
     * @param string $consumer The consumer app name
     * @param string $consumerVersion The version number of the application. Required. It is recommended that this should be or include the git SHA. See http://docs.pact.io/versioning.
     * @param string|null $consumerBranch The git branch name.
     * @param array $contracts The content of the contracts
     * @param array $consumerTags The consumer version tags. Use of the branch parameter is preferred now.
     * @param string|null $buildUrl The CI/CD build URL
     *
     * @return array List of notices returned from broker. Designed to be displayed in the output of a CLI.
     */
    public function contractsPublish(
        string  $consumer,
        string  $consumerVersion,
        ?string  $consumerBranch,
        array   $contracts,
        array   $consumerTags = [],
        ?string $buildUrl = null
    ): array;
}
