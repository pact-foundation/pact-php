<?php

namespace PhpPact\Consumer;

use PhpPact\Standalone\StubService\StubServerConfig;
use PHPUnit\Framework\TestCase;

class StubServerConfigTest extends TestCase
{
    public function testSetters()
    {
        $pactLocation = __DIR__.'/../../../_resources/someconsumer-someprovider.json';
        $host = 'test-host';
        $port = 1234;
        $log = 'test-log-dir/';

        $subject = (new StubServerConfig())
            ->setPactLocation($pactLocation)
            ->setHost($host)
            ->setPort($port)
            ->setLog($log);

        static::assertSame($pactLocation, $subject->getPactLocation());
        static::assertSame($host, $subject->getHost());
        static::assertSame($port, $subject->getPort());
        static::assertSame($log, $subject->getLog());
    }
}
