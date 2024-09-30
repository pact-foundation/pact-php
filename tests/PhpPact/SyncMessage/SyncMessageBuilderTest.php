<?php

namespace PhpPactTest\SyncMessage;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\ProviderState;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Standalone\MockService\Model\VerifyResult;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactoryInterface;
use PhpPact\SyncMessage\SyncMessageBuilder;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class SyncMessageBuilderTest extends TestCase
{
    private SyncMessageBuilder $builder;
    private SyncMessageDriverInterface&MockObject $driver;
    private MockServerConfigInterface&MockObject $config;
    private SyncMessageDriverFactoryInterface&MockObject $driverFactory;

    public function setUp(): void
    {
        $this->driver = $this->createMock(SyncMessageDriverInterface::class);
        $this->config = $this->createMock(MockServerConfigInterface::class);
        $this->driverFactory = $this->createMock(SyncMessageDriverFactoryInterface::class);
        $this->driverFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->config)
            ->willReturn($this->driver);
        $this->builder = new SyncMessageBuilder($this->config, $this->driverFactory);
    }

    public function testGiven(): void
    {
        $this->assertSame($this->builder, $this->builder->given('test', ['key' => 'value']));
        $message = $this->getMessage();
        $providerStates = $message->getProviderStates();
        $this->assertCount(1, $providerStates);
        $providerState = $providerStates[0];
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('test', $providerState->getName());
        $this->assertSame(['key' => 'value'], $providerState->getParams());
    }

    public function testExpectsToReceive(): void
    {
        $description = 'message description';
        $this->assertSame($this->builder, $this->builder->expectsToReceive($description));
        $message = $this->getMessage();
        $this->assertSame($description, $message->getDescription());
    }

    public function testWithMetadata(): void
    {
        $metadata = ['key' => 'value'];
        $this->assertSame($this->builder, $this->builder->withMetadata($metadata));
        $message = $this->getMessage();
        $this->assertSame($metadata, $message->getMetadata());
    }

    /**
     * @param null|class-string<Text> $contentClass
     */
    #[TestWith([null                                          , null])]
    #[TestWith([new Text('example', 'text/plain')             , null])]
    #[TestWith([new Binary('/path/to/image.jpg', 'image/jpeg'), null])]
    #[TestWith(['example text'                                , Text::class])]
    #[TestWith([['key' => 'value']                            , Text::class])]
    public function testWithContent(mixed $content, ?string $contentClass): void
    {
        $this->assertSame($this->builder, $this->builder->withContent($content));
        $message = $this->getMessage();
        if ($contentClass) {
            $this->assertInstanceOf($contentClass, $message->getContents());
        } else {
            $this->assertSame($content, $message->getContents());
        }
    }

    #[TestWith([null])]
    #[TestWith(['key'])]
    public function testSetKey(?string $key): void
    {
        $this->assertSame($this->builder, $this->builder->key($key));
        $message = $this->getMessage();
        $this->assertSame($key, $message->getKey());
    }

    #[TestWith([null])]
    #[TestWith([false])]
    #[TestWith([true])]
    public function testSetPending(?bool $pending): void
    {
        $this->assertSame($this->builder, $this->builder->pending($pending));
        $message = $this->getMessage();
        $this->assertSame($pending, $message->getPending());
    }

    /**
     * @param array<string, mixed> $comments
     */
    #[TestWith([[]])]
    #[TestWith([['key1' => null, 'key2' => 'value', 'key3' => ['value']]])]
    public function testSetComments(array $comments): void
    {
        $this->assertSame($this->builder, $this->builder->comments($comments));
        $message = $this->getMessage();
        $this->assertSame($comments, $message->getComments());
    }

    public function testAddTextComment(): void
    {
        $this->assertSame($this->builder, $this->builder->comment('comment 1'));
        $this->assertSame($this->builder, $this->builder->comment('comment 2'));
        $message = $this->getMessage();
        $this->assertSame(['comment 1', 'comment 2'], $message->getTextComments());
    }

    public function testRegisterMessage(): void
    {
        $message = $this->getMessage();
        $this->driver
            ->expects($this->once())
            ->method('registerMessage')
            ->with($message);
        $this->builder->registerMessage();
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testVerify(bool $matched): void
    {
        $this->driver
            ->expects($this->once())
            ->method('verifyMessage')
            ->willReturn(new VerifyResult($matched, ''));
        $this->assertSame($matched, $this->builder->verify());
    }

    private function getMessage(): Message
    {
        $reflection = new ReflectionProperty($this->builder, 'message');
        $message = $reflection->getValue($this->builder);
        $this->assertInstanceOf(Message::class, $message);

        return $message;
    }
}
