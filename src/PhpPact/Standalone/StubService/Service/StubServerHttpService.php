<?php

namespace PhpPact\Standalone\StubService\Service;

use PhpPact\Http\ClientInterface;
use PhpPact\Standalone\StubService\StubServerConfigInterface;

/**
 * Http Service that interacts with the Ruby Standalone Stub Server.
 *
 * @see https://github.com/pact-foundation/pact-stub_service
 */
class StubServerHttpService implements StubServerHttpServiceInterface
{
    private ClientInterface $client;

    private StubServerConfigInterface $config;

    public function __construct(ClientInterface $client, StubServerConfigInterface $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     * @throws \JsonException
     */
    public function getJson(string $endpoint): string
    {
        $uri      = $this->config->getBaseUri()->withPath('/' . $endpoint);
        $response = $this->client->get($uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        return \json_encode(\json_decode($response->getBody()->getContents(), null, 512, JSON_THROW_ON_ERROR), JSON_THROW_ON_ERROR);
    }
}
