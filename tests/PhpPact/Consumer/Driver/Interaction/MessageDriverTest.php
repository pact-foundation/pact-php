<?php

namespace PhpPactTest\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Interaction\MessageDriver;
use PhpPact\Consumer\Driver\Interaction\MessageDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\FFI\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MessageDriverTest extends TestCase
{
    private MessageDriverInterface $driver;
    private ClientInterface|MockObject $client;
    private PactDriverInterface|MockObject $pactDriver;
    private MessageBodyDriverInterface|MockObject $messageBodyDriver;
    private Message $message;
    private int $messageHandle = 123;
    private int $pactHandle = 234;
    private string $description = 'Receiving message';
    private array $providerStates = [
        'item exist' => [
            'id' => 12,
            'name' => 'abc',
        ]
    ];
    private array $metadata = [
        'key1' => 'value1',
        'key2' => 'value2',
    ];

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->pactDriver = $this->createMock(PactDriverInterface::class);
        $this->messageBodyDriver = $this->createMock(MessageBodyDriverInterface::class);
        $this->driver = new MessageDriver($this->client, $this->pactDriver, $this->messageBodyDriver);
        $this->message = new Message();
        $this->message->setDescription($this->description);
        foreach ($this->providerStates as $name => $params) {
            $this->message->addProviderState($name, $params);
        }
        $this->message->setMetadata($this->metadata);
    }

    public function testReify(): void
    {
        $this->message->setHandle($this->messageHandle);
        $result = 'message';
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_message_reify', $this->messageHandle)
            ->willReturn($result);
        $this->assertSame($result, $this->driver->reify($this->message));
    }

    public function testWritePactAndCleanUp(): void
    {
        $this->pactDriver
            ->expects($this->once())
            ->method('writePact');
        $this->pactDriver
            ->expects($this->once())
            ->method('cleanUp');
        $this->driver->writePactAndCleanUp();
    }

    public function testRegisterInteraction(): void
    {
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->messageBodyDriver
            ->expects($this->once())
            ->method('registerBody')
            ->with($this->message);
        $calls = [
            ['pactffi_new_message_interaction', $this->pactHandle, $this->description, $this->messageHandle],
            ['pactffi_message_given', $this->messageHandle, 'item exist', null],
            ['pactffi_message_given_with_param', $this->messageHandle, 'item exist', 'id', '12', null],
            ['pactffi_message_given_with_param', $this->messageHandle, 'item exist', 'name', 'abc', null],
            ['pactffi_message_expects_to_receive', $this->messageHandle, $this->description, null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key1', 'value1', null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key2', 'value2', null],
        ];
        $this->client
            ->expects($this->exactly(count($calls)))
            ->method('call')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                $return = array_pop($call);
                $this->assertSame($call, $args);

                return $return;
            });
        $this->driver->registerMessage($this->message);
        $this->assertSame($this->messageHandle, $this->message->getHandle());
    }
}
