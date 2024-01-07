<?php

namespace PhpPact\Plugins\Protobuf\Service;

use PhpPact\Consumer\Service\MockServer;

class GrpcMockServer extends MockServer
{
    protected function getTransport(): string
    {
        return 'grpc';
    }
}
