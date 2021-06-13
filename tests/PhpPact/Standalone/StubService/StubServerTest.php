<?php

namespace PhpPact\Standalone\StubService;

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

            $subject = (new StubServerConfig())
                ->setFiles($pactLocation)
                ->setPort($port);

            $stubServer = new StubServer($subject);
            $pid        = $stubServer->start();
            $this->assertTrue(\is_int($pid));
        } finally {
            $result = $stubServer->stop();
            $this->assertTrue($result);
        }
    }
}
