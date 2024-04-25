<?php

namespace PhpPactTest\Consumer\Factory;

use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Factory\InteractionDriverFactory;
use PhpPact\Consumer\Factory\InteractionDriverFactoryInterface;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPactTest\Helper\FactoryTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InteractionDriverFactoryTest extends TestCase
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
            ->willReturn('3.0.0');
    }

    public function testCreate(): void
    {
        $this->factory = new InteractionDriverFactory();
        $driver = $this->factory->create($this->config);
        $this->assertPropertiesInstanceOf($driver, null, [
            'client' => Client::class,
            'mockServer' => MockServer::class,
            'pactDriver' => PactDriver::class,
        ]);
    }
}
