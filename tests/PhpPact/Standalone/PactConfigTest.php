<?php

namespace PhpPact\Standalone;

use PhpPact\Standalone\PactConfig;
use PHPUnit\Framework\TestCase;

class PactConfigTest extends TestCase
{
    private PactConfigInterface $config;

    protected function setUp(): void
    {
        $this->config = new PactConfig();
    }

    public function testSetters(): void
    {
        $provider                 = 'test-provider';
        $consumer                 = 'test-consumer';
        $pactDir                  = 'test-pact-dir/';
        $pactSpecificationVersion = '2.0.0';
        $log                      = 'test-log-dir/';
        $logLevel                 = 'ERROR';
        $pactFileWriteMode        = 'merge';

        $this->config
            ->setProvider($provider)
            ->setConsumer($consumer)
            ->setPactDir($pactDir)
            ->setPactSpecificationVersion($pactSpecificationVersion)
            ->setLog($log)
            ->setLogLevel($logLevel)
            ->setPactFileWriteMode($pactFileWriteMode);

        static::assertSame($provider, $this->config->getProvider());
        static::assertSame($consumer, $this->config->getConsumer());
        static::assertSame($pactDir, $this->config->getPactDir());
        static::assertSame($pactSpecificationVersion, $this->config->getPactSpecificationVersion());
        static::assertSame($log, $this->config->getLog());
        static::assertSame($logLevel, $this->config->getLogLevel());
        static::assertSame($pactFileWriteMode, $this->config->getPactFileWriteMode());
    }

    public function testInvalidPactSpecificationVersion(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid version string "invalid"');
        $this->config->setPactSpecificationVersion('invalid');
    }

    public function testInvalidLogLevel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('LogLevel TRACE not supported.');
        $this->config->setLogLevel('TRACE');
    }

    public function testInvalidPactFileWriteMode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid PhpPact File Write Mode, value must be one of the following: overwrite, merge.");
        $this->config->setPactFileWriteMode('APPEND');
    }
}
