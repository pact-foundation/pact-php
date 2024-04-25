<?php

namespace PhpPactTest\Plugins\Protobuf\Factory;

use PhpPact\Consumer\Driver\Interaction\AbstractMessageDriver;
use PhpPact\FFI\Client;
use PhpPact\Plugins\Protobuf\Driver\Body\ProtobufMessageBodyDriver;
use PhpPact\Plugins\Protobuf\Driver\Pact\ProtobufPactDriver;
use PhpPact\Plugins\Protobuf\Factory\ProtobufSyncMessageDriverFactory;
use PhpPact\Plugins\Protobuf\Service\GrpcMockServer;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactoryInterface;
use PhpPactTest\Helper\FactoryTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProtobufSyncMessageDriverFactoryTest extends TestCase
{
    use FactoryTrait;

    private SyncMessageDriverFactoryInterface $factory;
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
        $this->factory = new ProtobufSyncMessageDriverFactory();
        $driver = $this->factory->create($this->config);
        $this->assertPropertiesInstanceOf($driver, null, [
            'client' => Client::class,
            'pactDriver' => ProtobufPactDriver::class,
            'mockServer' => GrpcMockServer::class,
        ]);
        $this->assertPropertiesInstanceOf($driver, AbstractMessageDriver::class, [
            'messageBodyDriver' => ProtobufMessageBodyDriver::class,
        ]);
    }
}
