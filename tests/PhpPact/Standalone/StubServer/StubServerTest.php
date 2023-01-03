<?php

namespace PhpPactTest\Standalone\StubServer;

use PhpPact\Standalone\StubService\StubServer;
use PhpPact\Standalone\StubService\StubServerConfig;
use PHPUnit\Framework\TestCase;

class StubServerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testStartAndStop()
    {
        try {
            $pactLocation = __DIR__ . '/../../../_resources/someconsumer-someprovider.json';
            $port         = 7201;
            $endpoint     = 'test';

            $subject = (new StubServerConfig())
                ->setFiles($pactLocation)
                ->setPort($port)
                ->setEndpoint($endpoint);

            $stubServer = new StubServer($subject);
            $pid        = $stubServer->start();
            $this->assertTrue(\is_int($pid));
        } finally {
            $result = $stubServer->stop();
            $this->assertTrue($result);
        }
    }
}
