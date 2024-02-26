<?php

namespace PhpPactTest\Plugins\Csv\Factory;

use PhpPact\Consumer\Driver\Body\InteractionBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\AbstractInteractionPartDriver;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriverInterface;
use PhpPact\Consumer\Factory\InteractionDriverFactoryInterface;
use PhpPact\Plugins\Csv\Driver\Body\CsvBodyDriver;
use PhpPact\Plugins\Csv\Exception\MissingPluginPartsException;
use PhpPact\Plugins\Csv\Factory\CsvInteractionDriverFactory;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class CsvInteractionDriverFactoryTest extends TestCase
{
    private InteractionDriverFactoryInterface $factory;
    private MockServerConfigInterface|MockObject $config;

    public function setUp(): void
    {
        $this->config = $this->createMock(MockServerConfigInterface::class);
        $this->config
            ->expects($this->any())
            ->method('getPactSpecificationVersion')
            ->willReturn('4.0.0');
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    #[TestWith([InteractionPart::REQUEST, InteractionPart::RESPONSE])]
    public function testCreate(InteractionPart ...$pluginParts): void
    {
        $this->factory = new CsvInteractionDriverFactory(...$pluginParts);
        $driver = $this->factory->create($this->config);
        $requestBodyDriver = $this->getBodyDriver($this->getRequestDriver($driver));
        if (in_array(InteractionPart::REQUEST, $pluginParts)) {
            $this->assertCsvBodyDriver($requestBodyDriver);
        } else {
            $this->assertNotCsvBodyDriver($requestBodyDriver);
        }
        $responseBodyDriver = $this->getBodyDriver($this->getResponseDriver($driver));
        if (in_array(InteractionPart::RESPONSE, $pluginParts)) {
            $this->assertCsvBodyDriver($responseBodyDriver);
        } else {
            $this->assertNotCsvBodyDriver($responseBodyDriver);
        }
    }

    public function testMissingPluginPartsException(): void
    {
        $this->expectException(MissingPluginPartsException::class);
        $this->expectExceptionMessage('At least 1 interaction part must be csv');
        $this->factory = new CsvInteractionDriverFactory();
        $this->factory->create($this->config);
    }

    private function getRequestDriver(InteractionDriverInterface $driver): RequestDriverInterface
    {
        $reflection = new ReflectionProperty($driver, 'requestDriver');

        return $reflection->getValue($driver);
    }

    private function getResponseDriver(InteractionDriverInterface $driver): ResponseDriverInterface
    {
        $reflection = new ReflectionProperty($driver, 'responseDriver');

        return $reflection->getValue($driver);
    }

    private function assertCsvBodyDriver(InteractionBodyDriverInterface $bodyDriver): void
    {
        $this->assertInstanceOf(CsvBodyDriver::class, $bodyDriver);
    }

    private function assertNotCsvBodyDriver(InteractionBodyDriverInterface $bodyDriver): void
    {
        $this->assertNotInstanceOf(CsvBodyDriver::class, $bodyDriver);
    }

    private function getBodyDriver(RequestDriverInterface|ResponseDriverInterface $interactionPartDriver): InteractionBodyDriverInterface
    {
        $reflection = new ReflectionProperty(AbstractInteractionPartDriver::class, 'bodyDriver');

        return $reflection->getValue($interactionPartDriver);
    }
}
