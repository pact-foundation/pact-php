<?php

namespace PhpPact\Consumer\TestCase;

use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\MockServer\MockServer;
use PhpPact\Standalone\MockServer\MockServerConfig;
use PhpPact\Standalone\MockServer\MockServerEnvConfig;
use PhpPact\Standalone\MockServer\Service\MockServerHttpService;
use PHPUnit\Framework\TestCase;

class MockServerTestCase extends TestCase
{
    /** @var MockServerConfig */
    protected $config;
    /** @var MockServer */
    private $mockServer;

    protected function setUp()
    {
        $this->config = new MockServerEnvConfig();
        $this->config
            ->setPactFileWriteMode('merge')
            ->setPactDir('D:/Development/PACT/pact-php');
        $this->mockServer = new MockServer($this->config);
        $this->mockServer->start();
    }

    protected function tearDown()
    {
        $httpService = new MockServerHttpService(new GuzzleClient(), $this->config);
        $httpService->verifyInteractions();
        $httpService->getPactJson();
        $this->mockServer->stop();
    }
}
