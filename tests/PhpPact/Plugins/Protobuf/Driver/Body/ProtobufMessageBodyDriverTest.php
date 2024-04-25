<?php

namespace PhpPactTest\Plugins\Protobuf\Driver\Body;

use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Message;
use PhpPact\Plugin\Driver\Body\PluginBodyDriverInterface;
use PhpPact\Plugins\Protobuf\Driver\Body\ProtobufMessageBodyDriver;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProtobufMessageBodyDriverTest extends TestCase
{
    private MessageBodyDriverInterface $driver;
    private PluginBodyDriverInterface&MockObject $decorated;

    public function setUp(): void
    {
        $this->decorated = $this->createMock(PluginBodyDriverInterface::class);
        $this->driver = new ProtobufMessageBodyDriver($this->decorated);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testRegisterBody(InteractionPart $part): void
    {
        $message = new Message();
        $this->decorated
            ->expects($this->once())
            ->method('registerBody')
            ->with($message, InteractionPart::REQUEST);
        $this->driver->registerBody($message);
    }
}
