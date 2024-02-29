<?php

namespace PhpPactTest\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Standalone\MockService\Model\VerifyResult;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriver;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SyncMessageDriverTest extends TestCase
{
    private SyncMessageDriverInterface $driver;
    private MockServerInterface|MockObject $mockServer;
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
        $this->mockServer = $this->createMock(MockServerInterface::class);
        $this->client = $this->createMock(ClientInterface::class);
        $this->pactDriver = $this->createMock(PactDriverInterface::class);
        $this->messageBodyDriver = $this->createMock(MessageBodyDriverInterface::class);
        $this->driver = new SyncMessageDriver($this->mockServer, $this->client, $this->pactDriver, $this->messageBodyDriver);
        $this->message = new Message();
        $this->message->setDescription($this->description);
        foreach ($this->providerStates as $name => $params) {
            $this->message->addProviderState($name, $params);
        }
        $this->message->setMetadata($this->metadata);
    }

    public function testVerifyMessage(): void
    {
        $result = new VerifyResult(false, 'some mismatches');
        $this->mockServer
            ->expects($this->once())
            ->method('verify')
            ->willReturn($result);
        $this->assertSame($result, $this->driver->verifyMessage());
    }

    public function testWritePactAndCleanUp(): void
    {
        $this->mockServer
            ->expects($this->once())
            ->method('writePact');
        $this->mockServer
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
            ['pactffi_new_sync_message_interaction', $this->pactHandle, $this->description, $this->messageHandle],
            ['pactffi_given', $this->messageHandle, 'item exist', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'id', '12', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'name', 'abc', null],
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
