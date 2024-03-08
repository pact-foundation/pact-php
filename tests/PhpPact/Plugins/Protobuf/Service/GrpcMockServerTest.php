<?php

namespace PhpPactTest\Plugins\Protobuf\Service;

use PhpPact\Plugins\Protobuf\Service\GrpcMockServer;
use PhpPactTest\Consumer\Service\MockServerTest;

class GrpcMockServerTest extends MockServerTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->mockServer = new GrpcMockServer($this->client, $this->pactDriver, $this->config);
    }

    protected function getTransport(bool $secure): string
    {
        return 'grpc';
    }
}
