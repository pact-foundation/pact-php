<?php

namespace PhpPact\Consumer;

use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class MockServerConfigTest extends TestCase
{
    public function testSetters()
    {
        $host                     = 'test-host';
        $port                     = 1234;
        $provider                 = 'test-provider';
        $consumer                 = 'test-consumer';
        $pactDir                  = 'test-pact-dir/';
        $pactFileWriteMode        = 'merge';
        $log                      = 'test-log-dir/';
        $cors                     = true;
        $pactSpecificationVersion = 2.0;

        $subject = (new MockServerConfig())
            ->setHost($host)
            ->setPort($port)
            ->setProvider($provider)
            ->setConsumer($consumer)
            ->setPactDir($pactDir)
            ->setPactFileWriteMode($pactFileWriteMode)
            ->setLog($log)
            ->setPactSpecificationVersion($pactSpecificationVersion)
            ->setCors($cors);

        static::assertSame($host, $subject->getHost());
        static::assertSame($port, $subject->getPort());
        static::assertSame($provider, $subject->getProvider());
        static::assertSame($consumer, $subject->getConsumer());
        static::assertSame($pactDir, $subject->getPactDir());
        static::assertSame($pactFileWriteMode, $subject->getPactFileWriteMode());
        static::assertSame($log, $subject->getLog());
        static::assertSame($pactSpecificationVersion, $subject->getPactSpecificationVersion());
        static::assertSame($cors, $subject->hasCors());
    }
}
