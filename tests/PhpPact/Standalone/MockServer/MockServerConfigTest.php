<?php

namespace PhpPactTest\Standalone\MockServer;

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
        $logLevel                 = 'INFO';
        $log                      = 'test-log-dir/';
        $pactSpecificationVersion = '3.0.0';
        $secure                   = false;

        $subject = new MockServerConfig();
        $subject->setHost($host)
            ->setPort($port)
            ->setProvider($provider)
            ->setConsumer($consumer)
            ->setPactDir($pactDir)
            ->setPactFileWriteMode($pactFileWriteMode)
            ->setLogLevel($logLevel)
            ->setLog($log)
            ->setPactSpecificationVersion($pactSpecificationVersion);

        static::assertSame($secure, $subject->isSecure());
        static::assertSame($host, $subject->getHost());
        static::assertSame($port, $subject->getPort());
        static::assertSame($provider, $subject->getProvider());
        static::assertSame($consumer, $subject->getConsumer());
        static::assertSame($pactDir, $subject->getPactDir());
        static::assertSame($pactFileWriteMode, $subject->getPactFileWriteMode());
        static::assertSame($log, $subject->getLog());
        static::assertSame($logLevel, $subject->getLogLevel());
        static::assertSame($pactSpecificationVersion, $subject->getPactSpecificationVersion());
    }
}
