<?php

namespace PhpPactTest\Helper\FFI;

use PhpPact\FFI\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;

trait ClientTrait
{
    protected ClientInterface|MockObject $client;

    protected function assertClientCalls(array $calls): void
    {
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('call')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                $return = array_pop($call);
                $this->assertSame($call, $args);

                return $return;
            });
    }
}
