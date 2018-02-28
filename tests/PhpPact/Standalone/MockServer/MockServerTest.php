<?php

namespace PhpPact\Consumer;

use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\MockService\MockServer;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class MockServerTest extends TestCase
{
    public function testStartAndStop()
    {
        try {
            $mockServer = new MockServer(new MockServerEnvConfig(), new InstallManager());
            $pid        = $mockServer->start();
            $this->assertTrue(\is_int($pid));
        } finally {
            $result = $mockServer->stop();
            $this->assertTrue($result);
        }
    }
}
