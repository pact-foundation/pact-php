<?php

namespace PhpPactTest\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Exception\InteractionCommentNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionKeyNotSetException;
use PhpPact\Consumer\Driver\Exception\InteractionPendingNotSetException;
use PhpPact\Consumer\Driver\Interaction\MessageDriver;
use PhpPact\Consumer\Driver\Interaction\MessageDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\FFI\ClientInterface;
use PhpPactTest\Helper\FFI\ClientTrait;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MessageDriverTest extends TestCase
{
    use ClientTrait;

    private MessageDriverInterface $driver;
    private PactDriverInterface&MockObject $pactDriver;
    private MessageBodyDriverInterface&MockObject $messageBodyDriver;
    private Message $message;
    private int $messageHandle = 123;
    private int $pactHandle = 234;
    private string $description = 'Receiving message';
    /**
     * @var array<string, array<string, string>>
     */
    private array $providerStates = [
        'item exist' => [
            'id' => '12',
            'name' => 'abc',
        ]
    ];
    /**
     * @var array<string, string>
     */
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
        $this->expectsMessageReify($this->messageHandle, $result);
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
        $this->expectsNewMessageInteraction($this->pactHandle, $this->description, $this->messageHandle);
        $this->expectsMessageGiven($this->messageHandle, 'item exist');
        $this->expectsMessageGivenWithParam($this->messageHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ]);
        $this->expectsMessageExpectsToReceive($this->messageHandle, $this->description);
        $this->expectsMessageWithMetadataV2($this->messageHandle, $this->metadata);
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
        $this->expectsNewMessageInteraction($this->pactHandle, $this->description, $this->messageHandle);
        $this->expectsMessageGiven($this->messageHandle, 'item exist');
        $this->expectsMessageGivenWithParam($this->messageHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ]);
        $this->expectsMessageExpectsToReceive($this->messageHandle, $this->description);
        $this->expectsMessageWithMetadataV2($this->messageHandle, $this->metadata);
        $this->expectsSetInteractionKey($this->messageHandle, $this->description, $key, $success);
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
        $this->expectsNewMessageInteraction($this->pactHandle, $this->description, $this->messageHandle);
        $this->expectsMessageGiven($this->messageHandle, 'item exist');
        $this->expectsMessageGivenWithParam($this->messageHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ]);
        $this->expectsMessageExpectsToReceive($this->messageHandle, $this->description);
        $this->expectsMessageWithMetadataV2($this->messageHandle, $this->metadata);
        $this->expectsSetInteractionPending($this->messageHandle, $this->description, $pending, $success);
        $this->driver->registerMessage($this->message);
    }

    /**
     * @param array<string, mixed> $comments
     */
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
        $this->expectsNewMessageInteraction($this->pactHandle, $this->description, $this->messageHandle);
        $this->expectsMessageGiven($this->messageHandle, 'item exist');
        $this->expectsMessageGivenWithParam($this->messageHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ]);
        $this->expectsMessageExpectsToReceive($this->messageHandle, $this->description);
        $this->expectsMessageWithMetadataV2($this->messageHandle, $this->metadata);
        $this->expectsSetComments($this->messageHandle, $this->description, $comments, $success);
        $this->driver->registerMessage($this->message);
    }

    /**
     * @param string[] $comments
     */
    #[TestWith([['comment 1', 'comment 2'], false])]
    #[TestWith([['comment 1', 'comment 2'], true])]
    public function testAddTextComment(array $comments, bool $success): void
    {
        foreach ($comments as $comment) {
            $this->message->addTextComment($comment);
        }
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->expectsNewMessageInteraction($this->pactHandle, $this->description, $this->messageHandle);
        $this->expectsMessageGiven($this->messageHandle, 'item exist');
        $this->expectsMessageGivenWithParam($this->messageHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ]);
        $this->expectsMessageExpectsToReceive($this->messageHandle, $this->description);
        $this->expectsMessageWithMetadataV2($this->messageHandle, $this->metadata);
        $this->expectsAddTextComments($this->messageHandle, $this->description, $comments, $success);
        $this->driver->registerMessage($this->message);
    }
}
