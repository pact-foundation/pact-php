<?php

namespace PhpPactTest\SyncMessage\Factory;

use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactory;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactoryInterface;
use PhpPactTest\Helper\FactoryTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SyncMessageDriverFactoryTest extends TestCase
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
            ->willReturn('3.0.0');
    }

    public function testCreate(): void
    {
        $this->factory = new SyncMessageDriverFactory();
        $driver = $this->factory->create($this->config);
        $this->assertPropertiesInstanceOf($driver, null, [
            'client' => Client::class,
            'mockServer' => MockServer::class,
            'pactDriver' => PactDriver::class,
        ]);
    }
}
