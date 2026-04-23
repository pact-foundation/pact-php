<?php

namespace PhpPactTest\CompatibilitySuite\Plugin;

use PhpPact\Consumer\Service\MockServer;

class TcpMockServer extends MockServer
{
    protected function getTransport(): string
    {
        return 'tcp';
    }
}
