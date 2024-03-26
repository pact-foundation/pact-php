<?php

namespace StubServerConsumer\Tests;

use StubServerConsumer\Service\HttpClientService;
use PhpPact\Standalone\StubService\StubServer;
use PhpPact\Standalone\StubService\StubServerConfig;
use PHPUnit\Framework\TestCase;

class StubServerTest extends TestCase
{
    public function testStubServer(): void
    {
        try {
            $dirs = [__DIR__ . '/../_resources'];
            $extension = 'json';
            $port = 7201;
            $logLevel = 'debug';

            $config = (new StubServerConfig())
                ->setDirs($dirs)
                ->setExtension($extension)
                ->setPort($port)
                ->setLogLevel($logLevel);

            $stubServer = new StubServer($config);
            $pid        = $stubServer->start();
            $this->assertIsInt($pid);

            $service = new HttpClientService($config->getBaseUri());
            $results = $service->getResults();
            $this->assertEquals([
                (object) [
                    'name' => 'Games'
                ]
            ], $results);
        } finally {
            $result = $stubServer->stop();
            $this->assertTrue($result);
        }
    }
}
