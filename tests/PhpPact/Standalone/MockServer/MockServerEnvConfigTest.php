<?php

namespace PhpPactTest\Standalone\MockServer;

use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MockServerEnvConfigTest extends TestCase
{
    #[TestWith(['PACT_MOCK_SERVER_HOST', 'localhost'])]
    #[TestWith(['PACT_MOCK_SERVER_HOST=example.test', 'example.test'])]
    public function testHost(string $assignment, string $host): void
    {
        $this->preserveEnv('PACT_MOCK_SERVER_PORT', $assignment, function () use ($host): void {
            $config = new MockServerEnvConfig();
            $this->assertSame($host, $config->getHost());
        });
    }

    #[TestWith(['PACT_MOCK_SERVER_PORT', 0])]
    #[TestWith(['PACT_MOCK_SERVER_PORT=123', 123])]
    public function testPort(string $assignment, int $port): void
    {
        $this->preserveEnv('PACT_MOCK_SERVER_PORT', $assignment, function () use ($port): void {
            $config = new MockServerEnvConfig();
            $this->assertSame($port, $config->getPort());
        });
    }

    #[TestWith(['PACT_CONSUMER_NAME', null])]
    #[TestWith(['PACT_CONSUMER_NAME=consumer', 'consumer'])]
    public function testConsumer(string $assignment, ?string $consumer): void
    {
        $this->preserveEnv('PACT_CONSUMER_NAME', $assignment, function () use ($consumer): void {
            if (!$consumer) {
                $this->expectException(MissingEnvVariableException::class);
                $this->expectExceptionMessage('Please provide required environmental variable PACT_CONSUMER_NAME!');
            }
            $config = new MockServerEnvConfig();
            $this->assertSame($consumer, $config->getConsumer());
        });
    }

    #[TestWith(['PACT_PROVIDER_NAME', null])]
    #[TestWith(['PACT_PROVIDER_NAME=provider', 'provider'])]
    public function testProvider(string $assignment, ?string $provider): void
    {
        $this->preserveEnv('PACT_PROVIDER_NAME', $assignment, function () use ($provider): void {
            if (!$provider) {
                $this->expectException(MissingEnvVariableException::class);
                $this->expectExceptionMessage('Please provide required environmental variable PACT_PROVIDER_NAME!');
            }
            $config = new MockServerEnvConfig();
            $this->assertSame($provider, $config->getProvider());
        });
    }

    #[TestWith(['PACT_OUTPUT_DIR', null])]
    #[TestWith(['PACT_OUTPUT_DIR=/path/to/pact/dir', '/path/to/pact/dir'])]
    public function testPactDir(string $assignment, ?string $pactDir): void
    {
        $this->preserveEnv('PACT_OUTPUT_DIR', $assignment, function () use ($pactDir): void {
            $pactDir ??= \sys_get_temp_dir();
            $config = new MockServerEnvConfig();
            $this->assertSame($pactDir, $config->getPactDir());
        });
    }

    #[TestWith(['PACT_LOG', null])]
    #[TestWith(['PACT_LOG=/path/to/log/dir', '/path/to/log/dir'])]
    public function testLog(string $assignment, ?string $logDir): void
    {
        $this->preserveEnv('PACT_LOG', $assignment, function () use ($logDir): void {
            $config = new MockServerEnvConfig();
            $this->assertSame($logDir, $config->getLog());
        });
    }

    #[TestWith(['PACT_LOGLEVEL', null])]
    #[TestWith(['PACT_LOGLEVEL=trace', 'TRACE'])]
    public function testLogLevel(string $assignment, ?string $logLevel): void
    {
        $this->preserveEnv('PACT_LOGLEVEL', $assignment, function () use ($logLevel): void {
            $config = new MockServerEnvConfig();
            $this->assertSame($logLevel, $config->getLogLevel());
        });
    }

    #[TestWith(['PACT_SPECIFICATION_VERSION', '3.0.0'])]
    #[TestWith(['PACT_SPECIFICATION_VERSION=1.1.0', '1.1.0'])]
    public function testPactSpecificationVersion(string $assignment, ?string $specificationVersion): void
    {
        $this->preserveEnv('PACT_SPECIFICATION_VERSION', $assignment, function () use ($specificationVersion): void {
            $config = new MockServerEnvConfig();
            $this->assertSame($specificationVersion, $config->getPactSpecificationVersion());
        });
    }

    private function preserveEnv(string $env, string $assignment, callable $callback): void
    {
        $value = getenv($env);
        putenv($assignment);
        $callback();
        putenv("$env=$value");
    }
}
