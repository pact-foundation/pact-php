<?php

namespace PhpPact\Standalone\StubService\Service;

use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\StubService\StubServer;
use PhpPact\Standalone\StubService\StubServerConfig;
use PhpPact\Standalone\StubService\StubServerConfigInterface;
use PHPUnit\Framework\TestCase;

class StubServerHttpServiceTest extends TestCase
{
    /** @var StubServerHttpServiceInterface */
    private $service;

    /** @var StubServer */
    private $stubServer;

    /** @var StubServerConfigInterface */
    private $config;

    /**
     * @throws MissingEnvVariableException
     * @throws \Exception
     */
    protected function setUp()
    {
        $pactLocation             = __DIR__ . '/../../../../_resources/someconsumer-someprovider.json';
        $host                     = 'localhost';
        $port                     = 7201;
        $endpoint                 = 'test';

        $this->config = (new StubServerConfig())
            ->setPactLocation($pactLocation)
            ->setHost($host)
            ->setPort($port)
            ->setEndpoint($endpoint);

        $this->stubServer = new StubServer($this->config);
        $this->stubServer->start();
        $this->service = new StubServerHttpService(new GuzzleClient(), $this->config);
    }

    protected function tearDown()
    {
        $this->stubServer->stop();
    }

    public function testGetJson()
    {
        $result = $this->service->getJson();
        $this->assertEquals('{"results":[{"name":"Games"}]}', $result);
    }
}
