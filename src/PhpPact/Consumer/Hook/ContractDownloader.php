<?php

namespace PhpPact\Consumer\Hook;

use Exception;
use PhpPact\Http\ClientInterface;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PhpPact\Standalone\MockService\Service\MockServerHttpService;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Runner\AfterLastTestHook;
use RuntimeException;

class ContractDownloader implements AfterLastTestHook
{
    /** @var MockServerConfigInterface */
    private $mockServerConfig;

    /** @var null|ClientInterface */
    private $client;

    /**
     * ContractDownloader constructor.
     *
     * @throws MissingEnvVariableException
     */
    public function __construct()
    {
        $this->mockServerConfig = new MockServerEnvConfig();
    }

    /**
     * @throws AssertionFailedError
     * @throws RuntimeException
     */
    public function executeAfterLastTest(): void
    {
        try {
            $this->getMockServerService()->verifyInteractions();
        } catch (Exception $e) {
            throw new AssertionFailedError('Pact interaction verification failed', 0, $e);
        }

        try {
            \file_put_contents($this->getPactFilename(), $this->getPactJson());
        } catch (Exception $e) {
            throw new RuntimeException('Pact contract generation failed', 0, $e);
        }
    }

    private function getMockServerService(): MockServerHttpService
    {
        return new MockServerHttpService(
            $this->getClient(),
            $this->mockServerConfig
        );
    }

    private function getClient(): ClientInterface
    {
        if (!$this->client) {
            $this->client = new GuzzleClient();
        }

        return $this->client;
    }

    private function getPactFilename(): string
    {
        return $this->mockServerConfig->getPactDir()
        . DIRECTORY_SEPARATOR
        . $this->mockServerConfig->getConsumer()
        . '-'
        . $this->mockServerConfig->getProvider() . '.json';
    }

    private function getPactJson(): string
    {
        $uri      = $this->mockServerConfig->getBaseUri()->withPath('/pact');
        $response = $this->getClient()->post(
            $uri,
            [
                'headers' => [
                    'Content-Type'        => 'application/json',
                    'X-Pact-Mock-Service' => true,
                ],
                'body' => \json_encode([
                    'consumer' => ['name' => $this->mockServerConfig->getConsumer()],
                    'provider' => ['name' => $this->mockServerConfig->getProvider()]
                ])
            ]
        );

        return \json_encode(\json_decode($response->getBody()->getContents()));
    }
}
