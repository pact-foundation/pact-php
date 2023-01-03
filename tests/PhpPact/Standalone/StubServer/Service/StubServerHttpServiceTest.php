<?php

namespace PhpPactTest\Standalone\StubServer\Service;

use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Standalone\StubService\Service\StubServerHttpService;
use PhpPact\Standalone\StubService\Service\StubServerHttpServiceInterface;
use PhpPact\Standalone\StubService\StubServer;
use PhpPact\Standalone\StubService\StubServerConfig;
use PhpPact\Standalone\StubService\StubServerConfigInterface;
use PHPUnit\Framework\TestCase;

class StubServerHttpServiceTest extends TestCase
{
    /** @var StubServerHttpServiceInterface */
    private StubServerHttpServiceInterface $service;

    /** @var StubServer */
    private StubServer $stubServer;

    /** @var StubServerConfigInterface */
    private StubServerConfigInterface $config;

    /**
     * @throws MissingEnvVariableException
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $pactLocation = __DIR__ . '/../../../../_resources/someconsumer-someprovider.json';
        $port         = 7201;
        $endpoint     = 'test';

        $this->config = (new StubServerConfig())
            ->setFiles($pactLocation)
            ->setPort($port)
            ->setEndpoint($endpoint);

        $this->stubServer = new StubServer($this->config);
        $this->stubServer->start();
        $this->service = new StubServerHttpService(new GuzzleClient(), $this->config);
    }

    protected function tearDown(): void
    {
        $this->stubServer->stop();
    }

    public function testGetJson()
    {
        $result = $this->service->getJson();
        $this->assertEquals('{"results":[{"name":"Games"}]}', $result);
    }
}
