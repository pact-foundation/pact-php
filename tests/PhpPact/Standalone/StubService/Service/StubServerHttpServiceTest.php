<?php

namespace PhpPact\Standalone\StubService\Service;

use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Standalone\StubService\StubServer;
use PhpPact\Standalone\StubService\StubServerConfig;
use PhpPact\Standalone\StubService\StubServerConfigInterface;
use PHPUnit\Framework\TestCase;

class StubServerHttpServiceTest extends TestCase
{
    private StubServerHttpServiceInterface $service;
    private StubServer $stubServer;
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
