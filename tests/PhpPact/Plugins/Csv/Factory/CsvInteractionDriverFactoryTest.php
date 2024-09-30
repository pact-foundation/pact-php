<?php

namespace PhpPactTest\Plugins\Csv\Factory;

use PhpPact\Consumer\Driver\Body\InteractionBodyDriver;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\AbstractInteractionPartDriver;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriver;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriver;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriverInterface;
use PhpPact\Consumer\Factory\InteractionDriverFactoryInterface;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\FFI\Client;
use PhpPact\Plugins\Csv\Driver\Body\CsvBodyDriver;
use PhpPact\Plugins\Csv\Driver\Pact\CsvPactDriver;
use PhpPact\Plugins\Csv\Exception\MissingPluginPartsException;
use PhpPact\Plugins\Csv\Factory\CsvInteractionDriverFactory;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPactTest\Helper\FactoryTrait;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class CsvInteractionDriverFactoryTest extends TestCase
{
    use FactoryTrait;

    private InteractionDriverFactoryInterface $factory;
    private MockServerConfigInterface&MockObject $config;

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
        $this->assertPropertiesInstanceOf($driver, null, [
            'client' => Client::class,
            'mockServer' => MockServer::class,
            'pactDriver' => CsvPactDriver::class,
            'requestDriver' => RequestDriver::class,
            'responseDriver' => ResponseDriver::class,
        ]);
        $requestDriver = $this->getRequestDriver($driver);
        $this->assertPropertiesInstanceOf($requestDriver, AbstractInteractionPartDriver::class, [
            'client' => Client::class,
            'bodyDriver' => in_array(InteractionPart::REQUEST, $pluginParts) ? CsvBodyDriver::class : InteractionBodyDriver::class,
        ]);
        $responseDriver = $this->getResponseDriver($driver);
        $this->assertPropertiesInstanceOf($responseDriver, AbstractInteractionPartDriver::class, [
            'client' => Client::class,
            'bodyDriver' => in_array(InteractionPart::RESPONSE, $pluginParts) ? CsvBodyDriver::class : InteractionBodyDriver::class,
        ]);
    }

    public function testMissingPluginPartsException(): void
    {
        $this->expectException(MissingPluginPartsException::class);
        $this->expectExceptionMessage('At least 1 interaction part must be csv');
        $this->factory = new CsvInteractionDriverFactory();
    }

    private function getRequestDriver(InteractionDriverInterface $driver): RequestDriverInterface
    {
        $reflection = new ReflectionProperty($driver, 'requestDriver');
        $requestDriver = $reflection->getValue($driver);
        $this->assertInstanceOf(RequestDriverInterface::class, $requestDriver);

        return $requestDriver;
    }

    private function getResponseDriver(InteractionDriverInterface $driver): ResponseDriverInterface
    {
        $reflection = new ReflectionProperty($driver, 'responseDriver');
        $responseDriver = $reflection->getValue($driver);
        $this->assertInstanceOf(ResponseDriverInterface::class, $responseDriver);

        return $responseDriver;
    }
}
