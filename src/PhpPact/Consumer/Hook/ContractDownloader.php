<?php

namespace PhpPact\Consumer\Hook;

use PHPUnit\Runner\AfterLastTestHook;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PhpPact\Standalone\MockService\Service\MockServerHttpService;
use PhpPact\Http\GuzzleClient;
use RuntimeException;
use PHPUnit\Framework\AssertionFailedError;
use Exception;


class ContractDownloader implements AfterLastTestHook
{
    /** @var MockServerConfigInterface */
    private $mockServerConfig;

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
        try
        {
            $this->getMockServerService()->verifyInteractions();
        } catch (Exception $e) {
            throw new AssertionFailedError('Pact interaction verification failed', $e);
        }

        try
        {
            file_put_contents($this->getPactFilename(), $this->getPactJson());
        } catch (Exception $e) {
            throw new RuntimeException('Pact contract generation failed', $e);
        }
    }

    private function getMockServerService(): MockServerHttpService
    {
        return new MockServerHttpService(
            new GuzzleClient(),
            $this->mockServerConfig
        );
    }

    private function getPactFilename() : string
    {
        return $this->mockServerConfig->getPactDir()
        . DIRECTORY_SEPARATOR
        . $this->mockServerConfig->getConsumer()
        . '-'
        . $this->mockServerConfig->getProvider() . '.json';
    }

    private function getPactJson() : string
    {
        $uri      = $this->mockServerConfig->getBaseUri()->withPath('/pact');
        $response = $this->client->post(
            $uri,
            [
                'headers' => [
                    'Content-Type'        => 'application/json',
                    'X-Pact-Mock-Service' => true,
                ],
                'body' => json_encode([
                    'consumer' => ['name' => $this->mockServerConfig->getConsumer()],
                    'provider' => ['name' => $this->mockServerConfig->getProvider()]
                ])
            ]
        );

        return \json_encode(\json_decode($response->getBody()->getContents()));
    }
}
