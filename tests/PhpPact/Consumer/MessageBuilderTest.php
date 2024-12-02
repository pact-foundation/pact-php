<?php

namespace PhpPactTest\Consumer;

use Exception;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Interaction\MessageDriverInterface;
use PhpPact\Consumer\Exception\MissingCallbackException;
use PhpPact\Consumer\Factory\MessageDriverFactoryInterface;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\ProviderState;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;

class MessageBuilderTest extends TestCase
{
    private MessageBuilder $builder;
    private MessageDriverInterface&MockObject $driver;
    private PactConfigInterface&MockObject $config;
    private MessageDriverFactoryInterface&MockObject $driverFactory;

    public function setUp(): void
    {
        $this->driver = $this->createMock(MessageDriverInterface::class);
        $this->config = $this->createMock(PactConfigInterface::class);
        $this->driverFactory = $this->createMock(MessageDriverFactoryInterface::class);
        $this->driverFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->config)
            ->willReturn($this->driver);
        $this->builder = new MessageBuilder($this->config, $this->driverFactory);
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

    public function testSetSingleCallback(): void
    {
        $callbacks = [
            fn () => 'first',
            fn () => 'second',
            fn () => 'third',
            fn () => 'fourth',
        ];
        foreach ($callbacks as $callback) {
            $this->assertSame($this->builder, $this->builder->setCallback($callback));
        }
        $this->assertCallbacks([end($callbacks)]);
    }

    public function testSetMultipleCallbacks(): void
    {
        $callbacks = [
            'first callback' => fn () => 'first',
            'second callback' => fn () => 'second',
            'third callback' => fn () => 'third',
            'fourth callback' => fn () => 'fourth',
        ];
        foreach ($callbacks as $description => $callback) {
            $this->assertSame($this->builder, $this->builder->setCallback($callback, $description));
        }
        $this->assertCallbacks($callbacks);
    }

    public function testReify(): void
    {
        $jsonMessage = '{"key": "value"}';
        $message = $this->getMessage();
        $this->driver
            ->expects($this->once())
            ->method('registerMessage')
            ->with($message);
        $this->driver
            ->expects($this->once())
            ->method('reify')
            ->with($message)
            ->willReturn($jsonMessage);
        $this->assertSame($jsonMessage, $this->builder->reify());
    }

    public function testVerifyWithoutCallback(): void
    {
        $this->expectException(MissingCallbackException::class);
        $this->expectExceptionMessage('Callbacks need to exist to run verify.');
        $this->builder->verify();
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testVerifyMessage(bool $callbackThrowException): void
    {
        $jsonMessage = '{"key": "value"}';
        /**
         * @var MockObject&callable
         */
        $callback = $this->getMockBuilder(stdClass::class)
            ->addMethods(['__invoke'])
            ->getMock();
        $mocker = $callback
            ->expects($this->once())
            ->method('__invoke');
        if ($callbackThrowException) {
            $mocker->willThrowException(new Exception('something wrong'));
        }
        $this->driver
            ->expects($this->once())
            ->method('reify')
            ->willReturn($jsonMessage);
        $this->driver
            ->expects($callbackThrowException ? $this->never() : $this->once())
            ->method('writePactAndCleanUp');
        $this->assertSame(!$callbackThrowException, $this->builder->verifyMessage($callback, 'a callback'));
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testVerify(bool $callbackThrowException): void
    {
        $jsonMessage = '{"key": "value"}';
        /**
         * @var MockObject&callable
         */
        $callback = $this->getMockBuilder(stdClass::class)
            ->addMethods(['__invoke'])
            ->getMock();
        $mocker = $callback
            ->expects($this->once())
            ->method('__invoke');
        if ($callbackThrowException) {
            $mocker->willThrowException(new Exception('something wrong'));
        }
        $this->driver
            ->expects($this->once())
            ->method('reify')
            ->willReturn($jsonMessage);
        $this->driver
            ->expects($callbackThrowException ? $this->never() : $this->once())
            ->method('writePactAndCleanUp');
        $this->builder->setCallback($callback);
        $this->assertSame(!$callbackThrowException, $this->builder->verify());
    }

    private function getMessage(): Message
    {
        $reflection = new ReflectionProperty($this->builder, 'message');
        $message = $reflection->getValue($this->builder);
        $this->assertInstanceOf(Message::class, $message);

        return $message;
    }

    /**
     * @param callable[] $expectedCallbacks
     */
    private function assertCallbacks(array $expectedCallbacks): void
    {
        $reflection = new ReflectionProperty($this->builder, 'callback');
        $callbacks = $reflection->getValue($this->builder);
        $this->assertIsArray($callbacks);
        $this->assertSame($expectedCallbacks, $callbacks);
    }
}
