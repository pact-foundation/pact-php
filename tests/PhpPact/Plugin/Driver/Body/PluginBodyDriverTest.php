<?php

namespace PhpPactTest\Plugin\Driver\Body;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Exception\BodyNotSupportedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\FFI\ClientInterface;
use PhpPact\Plugin\Driver\Body\PluginBodyDriver;
use PhpPact\Plugin\Driver\Body\PluginBodyDriverInterface;
use PhpPactTest\Helper\FFI\ClientTrait;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class PluginBodyDriverTest extends TestCase
{
    use ClientTrait;

    private PluginBodyDriverInterface $driver;
    private Interaction $interaction;
    private int $requestPartId = 1;
    private int $responsePartId = 2;
    private int $interactionId = 123;
    private Message $message;
    private int $messageId = 234;
    private Binary $binary;
    private Text $text;
    private Text $json;
    private Multipart $multipart;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->driver = new PluginBodyDriver($this->client);
        $this->interaction = new Interaction();
        $this->interaction->setHandle($this->interactionId);
        $this->interaction->setRequest(new ConsumerRequest());
        $this->interaction->setResponse(new ProviderResponse());
        $this->message = new Message();
        $this->message->setHandle($this->messageId);
        $this->binary = new Binary(__DIR__ . '/../../../../_resources/image.jpg', 'image/jpeg');
        $this->text = new Text('example', 'text/plain');
        $this->json = new Text('{}', 'application/json');
        $this->multipart = new Multipart([], 'abcde12345');
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testInteractionBinaryBody(InteractionPart $part): void
    {
        $this->expectsGetInteractionPartEnumMethods($part);
        if ($part === InteractionPart::REQUEST) {
            $this->interaction->getRequest()->setBody($this->binary);
        } else {
            $this->interaction->getResponse()->setBody($this->binary);
        }
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin does not support binary body');
        $this->driver->registerBody($this->interaction, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testMessageBinaryBody(InteractionPart $part): void
    {
        $this->expectsGetInteractionPartEnumMethods(InteractionPart::REQUEST);
        $this->message->setContents($this->binary);
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin does not support binary body');
        $this->driver->registerBody($this->message, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testInteractionPlainTextBody(InteractionPart $part): void
    {
        $this->expectsGetInteractionPartEnumMethods($part);
        if ($part === InteractionPart::REQUEST) {
            $this->interaction->getRequest()->setBody($this->text);
        } else {
            $this->interaction->getResponse()->setBody($this->text);
        }
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin only support json body contents');
        $this->driver->registerBody($this->interaction, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testMessagePlainTextBody(InteractionPart $part): void
    {
        $this->expectsGetInteractionPartEnumMethods(InteractionPart::REQUEST);
        $this->message->setContents($this->text);
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin only support json body contents');
        $this->driver->registerBody($this->message, $part);
    }

    #[TestWith([0])]
    #[TestWith([1])]
    #[TestWith([2])]
    #[TestWith([3])]
    #[TestWith([4])]
    #[TestWith([5])]
    #[TestWith([6])]
    #[TestWith([7])]
    public function testRequestJsonBody(int $error): void
    {
        $this->expectsGetInteractionPartEnumMethods(InteractionPart::REQUEST);
        $this->interaction->getRequest()->setBody($this->json);
        $this->expectsInteractionContents($this->interactionId, $this->requestPartId, $this->json->getContentType(), $this->json->getContents(), $error);
        $this->driver->registerBody($this->interaction, InteractionPart::REQUEST);
    }

    #[TestWith([0])]
    #[TestWith([1])]
    #[TestWith([2])]
    #[TestWith([3])]
    #[TestWith([4])]
    #[TestWith([5])]
    #[TestWith([6])]
    #[TestWith([7])]
    public function testResponseJsonBody(int $error): void
    {
        $this->expectsGetInteractionPartEnumMethods(InteractionPart::RESPONSE);
        $this->interaction->getResponse()->setBody($this->json);
        $this->expectsInteractionContents($this->interactionId, $this->responsePartId, $this->json->getContentType(), $this->json->getContents(), $error);
        $this->driver->registerBody($this->interaction, InteractionPart::RESPONSE);
    }

    #[TestWith([0])]
    #[TestWith([1])]
    #[TestWith([2])]
    #[TestWith([3])]
    #[TestWith([4])]
    #[TestWith([5])]
    #[TestWith([6])]
    #[TestWith([7])]
    public function testMessageJsonBody(int $error): void
    {
        $this->expectsGetInteractionPartEnumMethods(InteractionPart::REQUEST);
        $this->message->setContents($this->json);
        $this->expectsInteractionContents($this->messageId, $this->requestPartId, $this->json->getContentType(), $this->json->getContents(), $error);
        $this->driver->registerBody($this->message, InteractionPart::REQUEST);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testInteractionMultipartBody(InteractionPart $part): void
    {
        $this->expectsGetInteractionPartEnumMethods($part);
        if ($part === InteractionPart::REQUEST) {
            $this->interaction->getRequest()->setBody($this->multipart);
        } else {
            $this->interaction->getResponse()->setBody($this->multipart);
        }
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin does not support multipart body');
        $this->driver->registerBody($this->interaction, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testEmptyInteractionBody(InteractionPart $part): void
    {
        $this->expectsGetInteractionPartEnumMethods($part);
        $this->driver->registerBody($this->interaction, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testEmptyMessageBody(InteractionPart $part): void
    {
        $this->expectsGetInteractionPartEnumMethods(InteractionPart::REQUEST);
        $this->driver->registerBody($this->message, $part);
    }

    private function expectsGetInteractionPartEnumMethods(InteractionPart $part): void
    {
        if ($part === InteractionPart::REQUEST) {
            $this->client
                ->expects($this->once())
                ->method('getInteractionPartRequest')
                ->willReturn($this->requestPartId);
            $this->client->expects($this->never())->method('getInteractionPartResponse');
        } else {
            $this->client->expects($this->never())->method('getInteractionPartRequest');
            $this->client
                ->expects($this->once())
                ->method('getInteractionPartResponse')
                ->willReturn($this->responsePartId);
        }
    }
}
