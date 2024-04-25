<?php

namespace PhpPactTest\SyncMessage\Driver\Interaction;

use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Exception\InteractionCommentNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionKeyNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionPendingNotSetException;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Standalone\MockService\Model\VerifyResult;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriver;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PhpPactTest\Helper\FFI\ClientTrait;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SyncMessageDriverTest extends TestCase
{
    use ClientTrait;

    private SyncMessageDriverInterface $driver;
    private MockServerInterface&MockObject $mockServer;
    private PactDriverInterface&MockObject $pactDriver;
    private MessageBodyDriverInterface&MockObject $messageBodyDriver;
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
        $this->assertClientCalls($calls);
        $this->driver->registerMessage($this->message);
        $this->assertSame($this->messageHandle, $this->message->getHandle());
    }

    #[TestWith([null, true])]
    #[TestWith([null, true])]
    #[TestWith(['123ABC', false])]
    #[TestWith(['123ABC', true])]
    public function testSetKey(?string $key, bool $success): void
    {
        $this->message->setKey($key);
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $calls = [
            ['pactffi_new_sync_message_interaction', $this->pactHandle, $this->description, $this->messageHandle],
            ['pactffi_given', $this->messageHandle, 'item exist', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'id', '12', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'name', 'abc', null],
            ['pactffi_message_expects_to_receive', $this->messageHandle, $this->description, null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key1', 'value1', null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key2', 'value2', null],
        ];
        if (is_string($key)) {
            $calls[] = ['pactffi_set_key', $this->messageHandle, $key, $success];
        }
        if (!$success) {
            $this->expectException(InteractionKeyNotSetException::class);
            $this->expectExceptionMessage("Can not set the key '$key' for the interaction '{$this->description}'");
        }
        $this->assertClientCalls($calls);
        $this->driver->registerMessage($this->message);
    }

    #[TestWith([null, true])]
    #[TestWith([null, true])]
    #[TestWith([true, false])]
    #[TestWith([true, true])]
    #[TestWith([false, false])]
    #[TestWith([false, true])]
    public function testSetPending(?bool $pending, bool $success): void
    {
        $this->message->setPending($pending);
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $calls = [
            ['pactffi_new_sync_message_interaction', $this->pactHandle, $this->description, $this->messageHandle],
            ['pactffi_given', $this->messageHandle, 'item exist', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'id', '12', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'name', 'abc', null],
            ['pactffi_message_expects_to_receive', $this->messageHandle, $this->description, null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key1', 'value1', null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key2', 'value2', null],
        ];
        if (is_bool($pending)) {
            $calls[] = ['pactffi_set_pending', $this->messageHandle, $pending, $success];
        }
        if (!$success) {
            $this->expectException(InteractionPendingNotSetException::class);
            $this->expectExceptionMessage("Can not mark interaction '{$this->description}' as pending");
        }
        $this->assertClientCalls($calls);
        $this->driver->registerMessage($this->message);
    }

    #[TestWith([[], true])]
    #[TestWith([['key1' => null], true])]
    #[TestWith([['key1' => null], false])]
    #[TestWith([['key2' => 'string value'], true])]
    #[TestWith([['key2' => 'string value'], false])]
    #[TestWith([['key3' => ['value 1', 'value 2']], true])]
    #[TestWith([['key3' => ['value 1', 'value 2']], false])]
    public function testSetComments(array $comments, bool $success): void
    {
        $this->message->setComments($comments);
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $calls = [
            ['pactffi_new_sync_message_interaction', $this->pactHandle, $this->description, $this->messageHandle],
            ['pactffi_given', $this->messageHandle, 'item exist', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'id', '12', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'name', 'abc', null],
            ['pactffi_message_expects_to_receive', $this->messageHandle, $this->description, null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key1', 'value1', null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key2', 'value2', null],
        ];
        foreach ($comments as $key => $value) {
            $calls[] = ['pactffi_set_comment', $this->messageHandle, $key, (is_string($value) || is_null($value)) ? $value : json_encode($value), $success];
            if (!$success) {
                $this->expectException(InteractionCommentNotSetException::class);
                $this->expectExceptionMessage("Can not add comment '$key' to the interaction '{$this->description}'");
            }
        }
        $this->assertClientCalls($calls);
        $this->driver->registerMessage($this->message);
    }

    #[TestWith(['comment 1', false])]
    #[TestWith(['comment 2', true])]
    public function testAddTextComment(string $comment, bool $success): void
    {
        $this->message->addTextComment($comment);
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $calls = [
            ['pactffi_new_sync_message_interaction', $this->pactHandle, $this->description, $this->messageHandle],
            ['pactffi_given', $this->messageHandle, 'item exist', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'id', '12', null],
            ['pactffi_given_with_param', $this->messageHandle, 'item exist', 'name', 'abc', null],
            ['pactffi_message_expects_to_receive', $this->messageHandle, $this->description, null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key1', 'value1', null],
            ['pactffi_message_with_metadata_v2', $this->messageHandle, 'key2', 'value2', null],
            ['pactffi_add_text_comment', $this->messageHandle, $comment, $success],
        ];
        if (!$success) {
            $this->expectException(InteractionCommentNotSetException::class);
            $this->expectExceptionMessage("Can not add text comment '$comment' to the interaction '{$this->description}'");
        }
        $this->assertClientCalls($calls);
        $this->driver->registerMessage($this->message);
    }
}
