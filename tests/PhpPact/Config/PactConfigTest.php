<?php

namespace PhpPactTest\Config;

use PhpPact\Config\Enum\WriteMode;
use PhpPact\Config\Exception\InvalidWriteModeException;
use PhpPact\Config\PactConfig;
use PhpPact\Config\PactConfigInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class PactConfigTest extends TestCase
{
    protected PactConfigInterface $config;

    protected function setUp(): void
    {
        $this->config = new PactConfig();
    }

    public function testSetters(): void
    {
        $provider                 = 'test-provider';
        $consumer                 = 'test-consumer';
        $pactDir                  = 'test-pact-dir/';
        $pactSpecificationVersion = '3.0.0';
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
        static::assertSame(WriteMode::tryFrom($pactFileWriteMode), $this->config->getPactFileWriteMode());
    }

    public function testInvalidPactSpecificationVersion(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid version string "invalid"');
        $this->config->setPactSpecificationVersion('invalid');
    }

    #[TestWith(['trace', 'TRACE'])]
    #[TestWith(['debug', 'DEBUG'])]
    #[TestWith(['info', 'INFO'])]
    #[TestWith(['warn', 'WARN'])]
    #[TestWith(['error', 'ERROR'])]
    #[TestWith(['off', 'OFF'])]
    #[TestWith(['none', 'NONE'])]
    #[TestWith(['verbose', null])]
    public function testLogLevel(string $logLevel, ?string $result): void
    {
        if (!$result) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage('LogLevel VERBOSE not supported.');
        }
        $this->config->setLogLevel($logLevel);
        $this->assertSame($result, $this->config->getLogLevel());
    }

    public function testInvalidPactFileWriteMode(): void
    {
        $this->expectException(InvalidWriteModeException::class);
        $this->expectExceptionMessage("Mode 'APPEND' is not supported. Supported modes are: overwrite, merge");
        $this->config->setPactFileWriteMode('APPEND');
    }
}
