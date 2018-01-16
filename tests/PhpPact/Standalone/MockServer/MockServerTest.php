<?php

namespace PhpPact\Consumer;

use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\MockServer\MockServer;
use PhpPact\Standalone\MockServer\MockServerEnvConfig;
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
