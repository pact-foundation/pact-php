<?php

namespace PhpPact\Consumer;

use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Standalone\MockService\MockServer;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class MockServerTest extends TestCase
{
    /**
     * @throws MissingEnvVariableException
     * @throws \Exception
     */
    public function testStartAndStop()
    {
        try {
            $mockServer = new MockServer(new MockServerEnvConfig());
            $pid        = $mockServer->start();
            $this->assertTrue(\is_int($pid));
        } finally {
            $result = $mockServer->stop();
            $this->assertTrue($result);
        }
    }
}
