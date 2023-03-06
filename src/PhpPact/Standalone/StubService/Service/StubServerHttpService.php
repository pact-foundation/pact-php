<?php

namespace PhpPact\Standalone\StubService\Service;

use PhpPact\Http\ClientInterface;
use PhpPact\Standalone\StubService\StubServerConfigInterface;

/**
 * Http Service that interacts with the Ruby Standalone Stub Server.
 *
 * @see https://github.com/pact-foundation/pact-stub_service
 * Class StubServerHttpService
 */
class StubServerHttpService implements StubServerHttpServiceInterface
{
    /**
     * @var ClientInterface
     */
    private ClientInterface $client;

    /**
     * @var StubServerConfigInterface
     */
    private StubServerConfigInterface $config;

    /**
     * StubServerHttpService constructor.
     *
     * @param ClientInterface           $client
     * @param StubServerConfigInterface $config
     */
    public function __construct(ClientInterface $client, StubServerConfigInterface $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getJson(): string
    {
        $uri      = $this->config->getBaseUri()->withPath('/' . $this->config->getEndpoint());
        $response = $this->client->get($uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        return \json_encode(\json_decode($response->getBody()->getContents()));
    }
}
