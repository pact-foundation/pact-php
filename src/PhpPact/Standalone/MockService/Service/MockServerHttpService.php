<?php

namespace PhpPact\Standalone\MockService\Service;

use GuzzleHttp\Exception\RequestException;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\Message;
use PhpPact\Exception\ConnectionException;
use PhpPact\Http\ClientInterface;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectionException;

/**
 * Http Service that interacts with the Ruby Standalone Mock Server.
 *
 * @see https://github.com/pact-foundation/pact-mock_service
 * Class MockServerHttpService
 */
class MockServerHttpService implements MockServerHttpServiceInterface
{
    private ClientInterface $client;

    private MockServerConfigInterface $config;

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

        try {
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
        } catch (RequestException $e) {
            throw new ConnectionException('Failed to receive a successful response from the Mock Server.', $e);
        } catch (GuzzleConnectionException $e) {
            throw new ConnectionException('Failed to receive a successful response from the Mock Server.', $e);
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

        $this->client->get($uri, [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-Pact-Mock-Service' => true,
            ],
        ]);

        return true;
    }

    /**
     * {@inheritdoc}
     * @throws \JsonException
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

        return \json_encode(\json_decode($response->getBody()->getContents()), JSON_THROW_ON_ERROR);
    }

    /**
     * Wrapper for getPactJson to force the Ruby server to write the pact file to disk
     *
     * If the Pact-PHP does not gracefully kill the Ruby Server, it will not write the
     * file to disk.  This enables a work around.
     * @throws \JsonException
     */
    public function writePact(): string
    {
        return $this->getPactJson();
    }
}
