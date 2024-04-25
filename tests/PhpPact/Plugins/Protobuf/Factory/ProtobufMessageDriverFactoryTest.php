<?php

namespace PhpPactTest\Plugins\Protobuf\Factory;

use PhpPact\Consumer\Driver\Interaction\AbstractMessageDriver;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Factory\MessageDriverFactoryInterface;
use PhpPact\FFI\Client;
use PhpPact\Plugins\Protobuf\Driver\Body\ProtobufMessageBodyDriver;
use PhpPact\Plugins\Protobuf\Factory\ProtobufMessageDriverFactory;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPactTest\Helper\FactoryTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProtobufMessageDriverFactoryTest extends TestCase
{
    use FactoryTrait;

    private MessageDriverFactoryInterface $factory;
    private MockServerConfigInterface&MockObject $config;

    public function setUp(): void
    {
        $this->config = $this->createMock(MockServerConfigInterface::class);
        $this->config
            ->expects($this->any())
            ->method('getPactSpecificationVersion')
            ->willReturn('4.0.0');
    }

    public function testCreate(): void
    {
        $this->factory = new ProtobufMessageDriverFactory();
        $driver = $this->factory->create($this->config);
        $this->assertPropertiesInstanceOf($driver, AbstractMessageDriver::class, [
            'client' => Client::class,
            'pactDriver' => PactDriver::class,
            'messageBodyDriver' => ProtobufMessageBodyDriver::class,
        ]);
    }
}
