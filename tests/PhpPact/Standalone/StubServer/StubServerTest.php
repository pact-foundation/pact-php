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
            $files = [__DIR__ . '/../../../_resources/someconsumer-someprovider.json'];
            $port  = 7201;

            $subject = (new StubServerConfig())
                ->setFiles($files)
                ->setPort($port);

            $stubServer = new StubServer($subject);
            $pid        = $stubServer->start();
            $this->assertTrue(\is_int($pid));
        } finally {
            $result = $stubServer->stop();
            $this->assertTrue($result);
        }
    }

    /**
     * @throws \Exception
     */
    public function testRandomPort(): void
    {
        try {
            $files = [__DIR__ . '/../../../_resources/someconsumer-someprovider.json'];

            $subject = (new StubServerConfig())
                ->setFiles($files);

            $stubServer = new StubServer($subject);
            $stubServer->start();
            $this->assertGreaterThan(0, $subject->getPort());
        } finally {
            $result = $stubServer->stop();
            $this->assertTrue($result);
        }
    }
}
