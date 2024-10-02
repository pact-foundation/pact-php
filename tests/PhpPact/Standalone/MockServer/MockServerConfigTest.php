<?php

namespace PhpPactTest\Standalone\MockServer;

use PhpPact\Config\Enum\WriteMode;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MockServerConfigTest extends TestCase
{
    public function testSetters(): void
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
        static::assertSame(WriteMode::tryFrom($pactFileWriteMode), $subject->getPactFileWriteMode());
        static::assertSame($log, $subject->getLog());
        static::assertSame($logLevel, $subject->getLogLevel());
        static::assertSame($pactSpecificationVersion, $subject->getPactSpecificationVersion());
    }

    #[TestWith([false, 'http://example.test:123'])]
    #[TestWith([true, 'https://example.test:123'])]
    public function testGetBaseUri(bool $secure, string $baseUri): void
    {
        $config = new MockServerConfig();
        $config
            ->setHost('example.test')
            ->setPort(123)
            ->setSecure($secure);
        $this->assertEquals($baseUri, $config->getBaseUri());
    }
}
