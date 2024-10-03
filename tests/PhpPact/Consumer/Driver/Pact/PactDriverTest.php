<?php

namespace PhpPactTest\Consumer\Driver\Pact;

use PhpPact\Config\Enum\WriteMode;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Exception\MissingPactException;
use PhpPact\Consumer\Driver\Exception\PactFileNotWrittenException;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\FFI\ClientInterface;
use PhpPactTest\Helper\FFI\ClientTrait;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PactDriverTest extends TestCase
{
    use ClientTrait;

    protected const SPEC_UNKNOWN = 0;
    protected const SPEC_V1 = 1;
    protected const SPEC_V1_1 = 2;
    protected const SPEC_V2 = 3;
    protected const SPEC_V3 = 4;
    protected const SPEC_V4 = 5;
    protected PactDriverInterface $driver;
    protected PactConfigInterface&MockObject $config;
    protected int $pactHandle = 123;
    protected string $consumer = 'consumer';
    protected string $provider = 'provider';
    protected string $pactDir = '/path/to/pact/dir';

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->config = $this->createMock(PactConfigInterface::class);
        $this->expectsGetSpecificationEnumMethods();
        $this->driver = new PactDriver($this->client, $this->config);
    }

    #[TestWith([null   , '1.0.0', self::SPEC_V1])]
    #[TestWith(['trace', '1.1.0', self::SPEC_V1_1])]
    #[TestWith(['debug', '2.0.0', self::SPEC_V2])]
    #[TestWith(['info' , '3.0.0', self::SPEC_V3])]
    #[TestWith(['warn' , '4.0.0', self::SPEC_V4])]
    #[TestWith(['error', '1.0.0', self::SPEC_V1])]
    #[TestWith(['off'  , '1.1.0', self::SPEC_V1_1])]
    #[TestWith(['none' , '2.0.0', self::SPEC_V2])]
    #[TestWith([null   , '0.1.2', self::SPEC_UNKNOWN])]
    #[TestWith([null   , 'x.y.z', self::SPEC_UNKNOWN])]
    public function testSetUp(?string $logLevel, string $version, int $specificationHandle): void
    {
        $this->assertConfig($logLevel, $version);
        $this->expectsInitWithLogLevel($logLevel);
        $this->expectsNewPact($this->consumer, $this->provider, $this->pactHandle);
        $this->expectsWithSpecification($this->pactHandle, $specificationHandle, true);
        $this->driver->setUp();
        $this->assertSame($this->pactHandle, $this->driver->getPact()->handle);
    }

    public function testSetUpMultipleTimes(): void
    {
        $this->assertConfig(null, '1.0.0');
        $this->expectsNewPact($this->consumer, $this->provider, $this->pactHandle);
        $this->expectsWithSpecification($this->pactHandle, self::SPEC_V1, true);
        $this->driver->setUp();
        $this->driver->setUp();
        $this->driver->setUp();
    }

    public function testCleanUp(): void
    {
        $this->assertConfig(null, '1.0.0');
        $this->expectsNewPact($this->consumer, $this->provider, $this->pactHandle);
        $this->expectsWithSpecification($this->pactHandle, self::SPEC_V1, true);
        $this->expectsFreePactHandle($this->pactHandle, 0);
        $this->driver->setUp();
        $this->driver->cleanUp();
    }

    public function testCleanUpWithoutPact(): void
    {
        $this->expectException(MissingPactException::class);
        $this->driver->cleanUp();
    }

    public function testCanNotCleanUpPact(): void
    {
        $this->assertConfig(null, '1.0.0');
        $this->expectsNewPact($this->consumer, $this->provider, $this->pactHandle);
        $this->expectsWithSpecification($this->pactHandle, self::SPEC_V1, true);
        $this->expectsFreePactHandle($this->pactHandle, 1);
        $this->driver->setUp();
        $this->driver->cleanUp();
    }

    public function testGetPact(): void
    {
        $this->assertConfig(null, '1.0.0');
        $this->expectsNewPact($this->consumer, $this->provider, $this->pactHandle);
        $this->expectsWithSpecification($this->pactHandle, self::SPEC_V1, true);
        $this->driver->setUp();
        $pact = $this->driver->getPact();
        $this->assertSame($this->pactHandle, $pact->handle);
    }

    public function testGetPactWithoutPact(): void
    {
        $this->expectException(MissingPactException::class);
        $this->driver->getPact();
    }

    #[TestWith([0, WriteMode::OVERWRITE])]
    #[TestWith([1, WriteMode::OVERWRITE])]
    #[TestWith([2, WriteMode::OVERWRITE])]
    #[TestWith([3, WriteMode::OVERWRITE])]
    #[TestWith([4, WriteMode::OVERWRITE])]
    #[TestWith([0, WriteMode::MERGE])]
    #[TestWith([1, WriteMode::MERGE])]
    #[TestWith([2, WriteMode::MERGE])]
    #[TestWith([3, WriteMode::MERGE])]
    #[TestWith([4, WriteMode::MERGE])]
    public function testWritePact(int $error, WriteMode $writeMode): void
    {
        $this->assertConfig(null, '1.0.0');
        $this->config
            ->expects($this->once())
            ->method('getPactDir')
            ->willReturn($this->pactDir);
        $this->config
            ->expects($this->once())
            ->method('getPactFileWriteMode')
            ->willReturn($writeMode);
        $this->expectsNewPact($this->consumer, $this->provider, $this->pactHandle);
        $this->expectsWithSpecification($this->pactHandle, self::SPEC_V1, true);
        $this->expectsPactHandleWriteFile($this->pactHandle, $this->pactDir, $writeMode === WriteMode::OVERWRITE, $error);
        $this->driver->setUp();
        if ($error) {
            $this->expectException(PactFileNotWrittenException::class);
            $this->expectExceptionMessage(match ($error) {
                1 => 'The function panicked.',
                2 => 'The pact file was not able to be written.',
                3 => 'The pact for the given handle was not found.',
                default => 'Unknown error',
            });
        }
        $this->driver->writePact();
    }

    public function testWritePactWithoutPact(): void
    {
        $this->expectException(MissingPactException::class);
        $this->driver->writePact();
    }

    public function testWithSpecificationCanNotModifyPact(): void
    {
        $this->assertConfig(null, 'x.y.z');
        $this->expectsNewPact($this->consumer, $this->provider, $this->pactHandle);
        $this->expectsWithSpecification($this->pactHandle, self::SPEC_UNKNOWN, false);
        $this->driver->setUp();
    }

    protected function assertConfig(?string $logLevel, string $version): void
    {
        $this->config
            ->expects($this->once())
            ->method('getLogLevel')
            ->willReturn($logLevel);
        $this->config
            ->expects($this->any())
            ->method('getPactSpecificationVersion')
            ->willReturn($version);
        $this->config
            ->expects($this->once())
            ->method('getConsumer')
            ->willReturn($this->consumer);
        $this->config
            ->expects($this->once())
            ->method('getProvider')
            ->willReturn($this->provider);
    }

    private function expectsGetSpecificationEnumMethods(): void
    {
        $this->client
            ->expects($this->any())
            ->method('getPactSpecificationV1')
            ->willReturn(self::SPEC_V1);
        $this->client
            ->expects($this->any())
            ->method('getPactSpecificationV1_1')
            ->willReturn(self::SPEC_V1_1);
        $this->client
            ->expects($this->any())
            ->method('getPactSpecificationV2')
            ->willReturn(self::SPEC_V2);
        $this->client
            ->expects($this->any())
            ->method('getPactSpecificationV3')
            ->willReturn(self::SPEC_V3);
        $this->client
            ->expects($this->any())
            ->method('getPactSpecificationV4')
            ->willReturn(self::SPEC_V4);
        $this->client
            ->expects($this->any())
            ->method('getPactSpecificationUnknown')
            ->willReturn(self::SPEC_UNKNOWN);
    }
}
