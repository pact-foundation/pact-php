<?php

namespace PhpPact\Broker\Service;

use PhpPact\Http\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Interface for http interaction with the PACT Broker.
 * Interface BrokerHttpServiceInterface
 */
interface BrokerHttpServiceInterface
{
    /**
     * HttpServiceInterface constructor.
     *
     * @param ClientInterface $client  Http Client
     * @param UriInterface    $baseUri Base URI for the PhpPact Broker
     */
    public function __construct(ClientInterface $client, UriInterface $baseUri);

    /**
     * Publish JSON
     *
     * @param string $version Consumer version
     * @param string $json    PACT File JSON
     */
    public function publishJson(string $version, string $json);
}
