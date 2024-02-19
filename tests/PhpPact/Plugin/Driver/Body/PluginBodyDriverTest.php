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
use PhpPact\Plugin\Exception\PluginBodyNotAddedException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PluginBodyDriverTest extends TestCase
{
    private PluginBodyDriverInterface $driver;
    private ClientInterface|MockObject $client;
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
        $this->client
            ->expects($this->once())
            ->method('get')
            ->willReturnMap([
                ['InteractionPart_Request', $this->requestPartId],
                ['InteractionPart_Response', $this->responsePartId],
            ]);
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
        if ($part === InteractionPart::REQUEST) {
            $this->interaction->getRequest()->setBody($this->binary);
        } else {
            $this->interaction->getResponse()->setBody($this->binary);
        }
        $this->client
            ->expects($this->never())
            ->method('call');
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin does not support binary body');
        $this->driver->registerBody($this->interaction, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testMessageBinaryBody(InteractionPart $part): void
    {
        $this->message->setContents($this->binary);
        $this->client
            ->expects($this->never())
            ->method('call');
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin does not support binary body');
        $this->driver->registerBody($this->message, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testInteractionPlainTextBody(InteractionPart $part): void
    {
        if ($part === InteractionPart::REQUEST) {
            $this->interaction->getRequest()->setBody($this->text);
        } else {
            $this->interaction->getResponse()->setBody($this->text);
        }
        $this->client
            ->expects($this->never())
            ->method('call');
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin only support json body contents');
        $this->driver->registerBody($this->interaction, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testMessagePlainTextBody(InteractionPart $part): void
    {
        $this->message->setContents($this->text);
        $this->client
            ->expects($this->never())
            ->method('call');
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin only support json body contents');
        $this->driver->registerBody($this->message, $part);
    }

    private function getPluginBodyErrorMessage(int $error): string
    {
        return match ($error) {
            1 => 'A general panic was caught.',
            2 => 'The mock server has already been started.',
            3 => 'The interaction handle is invalid.',
            4 => 'The content type is not valid.',
            5 => 'The contents JSON is not valid JSON.',
            6 => 'The plugin returned an error.',
            default => 'Unknown error',
        };
    }

    #[DataProvider('errorProvider')]
    public function testRequestJsonBody(int $error): void
    {
        $this->interaction->getRequest()->setBody($this->json);
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_interaction_contents', $this->interactionId, $this->requestPartId, $this->json->getContentType(), $this->json->getContents())
            ->willReturn($error);
        if ($error) {
            $this->expectException(PluginBodyNotAddedException::class);
            $this->expectExceptionMessage($this->getPluginBodyErrorMessage($error));
        }
        $this->driver->registerBody($this->interaction, InteractionPart::REQUEST);
    }

    #[DataProvider('errorProvider')]
    public function testResponseJsonBody(int $error): void
    {
        $this->interaction->getResponse()->setBody($this->json);
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_interaction_contents', $this->interactionId, $this->responsePartId, $this->json->getContentType(), $this->json->getContents())
            ->willReturn($error);
        if ($error) {
            $this->expectException(PluginBodyNotAddedException::class);
            $this->expectExceptionMessage($this->getPluginBodyErrorMessage($error));
        }
        $this->driver->registerBody($this->interaction, InteractionPart::RESPONSE);
    }

    #[DataProvider('errorProvider')]
    public function testMessageJsonBody(int $error): void
    {
        $this->message->setContents($this->json);
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_interaction_contents', $this->messageId, $this->requestPartId, $this->json->getContentType(), $this->json->getContents())
            ->willReturn($error);
        if ($error) {
            $this->expectException(PluginBodyNotAddedException::class);
            $this->expectExceptionMessage($this->getPluginBodyErrorMessage($error));
        }
        $this->driver->registerBody($this->message, InteractionPart::REQUEST);
    }

    public static function errorProvider(): array
    {
        return [
            [0],
            [1],
            [2],
            [3],
            [4],
            [5],
            [6],
            [7],
        ];
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testInteractionMultipartBody(InteractionPart $part): void
    {
        if ($part === InteractionPart::REQUEST) {
            $this->interaction->getRequest()->setBody($this->multipart);
        } else {
            $this->interaction->getResponse()->setBody($this->multipart);
        }
        $this->client
            ->expects($this->never())
            ->method('call');
        $this->expectException(BodyNotSupportedException::class);
        $this->expectExceptionMessage('Plugin does not support multipart body');
        $this->driver->registerBody($this->interaction, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testEmptyInteractionBody(InteractionPart $part): void
    {
        $this->client
            ->expects($this->never())
            ->method('call');
        $this->driver->registerBody($this->interaction, $part);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testEmptyMessageBody(InteractionPart $part): void
    {
        $this->client
            ->expects($this->never())
            ->method('call');
        $this->driver->registerBody($this->message, $part);
    }
}
