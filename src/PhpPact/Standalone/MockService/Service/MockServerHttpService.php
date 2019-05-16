<?php

namespace PhpPact\Standalone\MockService\Service;

use GuzzleHttp\Exception\RequestException;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\Message;
use PhpPact\Exception\ConnectionException;
use PhpPact\Http\ClientInterface;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

/**
 * Http Service that interacts with the Ruby Standalone Mock Server.
 *
 * @see https://github.com/pact-foundation/pact-mock_service
 * Class MockServerHttpService
 */
class MockServerHttpService implements MockServerHttpServiceInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var MockServerConfigInterface
     */
    private $config;

    /**
     * MockServerHttpService constructor.
     *
     * @param ClientInterface           $client
     * @param MockServerConfigInterface $config
     */
    public function __construct(ClientInterface $client, MockServerConfigInterface $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function healthCheck(): bool
    {
        $uri = $this->config->getBaseUri()->withPath('/');

        $response = $this->client->get($uri, [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-Pact-Mock-Service' => true,
            ],
        ]);

        $body = $response->getBody()->getContents();

        if ($response->getStatusCode() !== 200
            || $body !== "Mock service running\n") {
            throw new ConnectionException('Failed to receive a successful response from the Mock Server.');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAllInteractions(): bool
    {
        $uri = $this->config->getBaseUri()->withPath('/interactions');

        $response = $this->client->delete($uri, [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-Pact-Mock-Service' => true,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function registerInteraction(Interaction $interaction): bool
    {
        $uri = $this->config->getBaseUri()->withPath('/interactions');

        $body = \json_encode($interaction->jsonSerialize());

        $this->client->post($uri, [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-Pact-Mock-Service' => true,
            ],
            'body' => $body,
        ]);

        return true;
    }

    /**
     * Separate function for messages, instead of interactions, as I am unsure what to do with the Ruby Standalone at the moment
     *
     * @param Message $message
     *
     * @return bool
     */
    public function registerMessage(Message $message): bool
    {
        $uri = $this->config->getBaseUri()->withPath('/interactions');

        $body = \json_encode($message->jsonSerialize());

        $this->client->post($uri, [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-Pact-Mock-Service' => true,
            ],
            'body' => $body,
        ]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function verifyInteractions(): bool
    {
        $uri = $this->config->getBaseUri()->withPath('/interactions/verification');

        try {
            $this->client->get(
                $uri, [
                'headers' => [
                    'Content-Type'        => 'application/json',
                    'X-Pact-Mock-Service' => true,
                ],
            ]
            );
        } catch (RequestException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getPactJson(): string
    {
        $uri      = $this->config->getBaseUri()->withPath('/pact');
        $response = $this->client->post($uri, [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-Pact-Mock-Service' => true,
            ],
        ]);

        return \json_encode(\json_decode($response->getBody()->getContents()));
    }

    /**
     * Wrapper for getPactJson to force the Ruby server to write the pact file to disk
     *
     * If the Pact-PHP does not gracefully kill the Ruby Server, it will not write the
     * file to disk.  This enables a work around.
     */
    public function writePact(): string
    {
        return $this->getPactJson();
    }
}
