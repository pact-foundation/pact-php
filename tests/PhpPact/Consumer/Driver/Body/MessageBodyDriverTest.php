<?php

namespace PhpPactTest\Consumer\Driver\Body;

use PhpPact\Consumer\Driver\Body\MessageBodyDriver;
use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Exception\MessageContentsNotAddedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\Message;
use PhpPact\FFI\ClientInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MessageBodyDriverTest extends TestCase
{
    private MessageBodyDriverInterface $driver;
    private ClientInterface|MockObject $client;
    private Message $message;
    private int $requestPartId = 1;
    private int $messageId = 123;
    private Binary $binary;
    private Text $text;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->driver = new MessageBodyDriver($this->client);
        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('InteractionPart_Request')
            ->willReturn($this->requestPartId);
        $this->message = new Message();
        $this->message->setHandle($this->messageId);
        $this->binary = new Binary(__DIR__ . '/../../../../_resources/image.jpg', 'image/jpeg');
        $this->text = new Text('example', 'text/plain');
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testMessageBinaryBody(bool $success): void
    {
        $data = $this->binary->getData();
        $this->message->setContents($this->binary);
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_with_binary_file', $this->messageId, $this->requestPartId, $this->binary->getContentType(), $data->getValue(), $data->getSize())
            ->willReturn($success);
        if (!$success) {
            $this->expectException(MessageContentsNotAddedException::class);
        }
        $this->driver->registerBody($this->message);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testMessageTextBody(bool $success): void
    {
        $this->message->setContents($this->text);
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_with_body', $this->messageId, $this->requestPartId, $this->text->getContentType(), $this->text->getContents())
            ->willReturn($success);
        if (!$success) {
            $this->expectException(MessageContentsNotAddedException::class);
        }
        $this->driver->registerBody($this->message);
    }

    public function testEmptyBody(): void
    {
        $this->client
            ->expects($this->never())
            ->method('call');
        $this->driver->registerBody($this->message);
    }
}
