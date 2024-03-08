<?php

namespace PhpPactTest\Consumer\Driver\Body;

use FFI;
use FFI\CData;
use PhpPact\Consumer\Driver\Body\InteractionBodyDriver;
use PhpPact\Consumer\Driver\Body\InteractionBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\Consumer\Exception\PartNotAddedException;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Part;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\FFI\ClientInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InteractionBodyDriverTest extends TestCase
{
    private InteractionBodyDriverInterface $driver;
    private ClientInterface|MockObject $client;
    private Interaction $interaction;
    private int $requestPartId = 1;
    private int $responsePartId = 2;
    private int $interactionHandle = 123;
    private Binary $binary;
    private Text $text;
    private Multipart $multipart;
    private array $parts;
    private string $boundary = 'abcde12345';
    private CData $failed;
    private string $message = 'error';

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
        $this->driver = new InteractionBodyDriver($this->client);
        $this->interaction = new Interaction();
        $this->interaction->setHandle($this->interactionHandle);
        $this->interaction->setRequest(new ConsumerRequest());
        $this->interaction->setResponse(new ProviderResponse());
        $this->binary = new Binary(__DIR__ . '/../../../../_resources/image.jpg', 'image/jpeg');
        $this->text = new Text('example', 'text/plain');
        $this->parts = [
            new Part('/path/to/id.txt', 'id', 'text/plain'),
            new Part('/path/to//address.json', 'address', 'application/json'),
            new Part('/path/to//image.png', 'profileImage', 'image/png'),
        ];
        $this->multipart = new Multipart($this->parts, $this->boundary);
        $this->failed = FFI::new('char[5]');
        FFI::memcpy($this->failed, $this->message, 5);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testRequestBinaryBody(bool $success): void
    {
        $data = $this->binary->getData();
        $this->interaction->getRequest()->setBody($this->binary);
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_with_binary_file', $this->interactionHandle, $this->requestPartId, $this->binary->getContentType(), $data->getValue(), $data->getSize())
            ->willReturn($success);
        if (!$success) {
            $this->expectException(InteractionBodyNotAddedException::class);
        }
        $this->driver->registerBody($this->interaction, InteractionPart::REQUEST);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testResponseBinaryBody(bool $success): void
    {
        $data = $this->binary->getData();
        $this->interaction->getResponse()->setBody($this->binary);
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_with_binary_file', $this->interactionHandle, $this->responsePartId, $this->binary->getContentType(), $data->getValue(), $data->getSize())
            ->willReturn($success);
        if (!$success) {
            $this->expectException(InteractionBodyNotAddedException::class);
        }
        $this->driver->registerBody($this->interaction, InteractionPart::RESPONSE);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testRequestTextBody(bool $success): void
    {
        $this->interaction->getRequest()->setBody($this->text);
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_with_body', $this->interactionHandle, $this->requestPartId, $this->text->getContentType(), $this->text->getContents())
            ->willReturn($success);
        if (!$success) {
            $this->expectException(InteractionBodyNotAddedException::class);
        }
        $this->driver->registerBody($this->interaction, InteractionPart::REQUEST);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testResponseTextBody(bool $success): void
    {
        $this->interaction->getResponse()->setBody($this->text);
        $this->client
            ->expects($this->once())
            ->method('call')
            ->with('pactffi_with_body', $this->interactionHandle, $this->responsePartId, $this->text->getContentType(), $this->text->getContents())
            ->willReturn($success);
        if (!$success) {
            $this->expectException(InteractionBodyNotAddedException::class);
        }
        $this->driver->registerBody($this->interaction, InteractionPart::RESPONSE);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testRequestMultipartBody(bool $success): void
    {
        $this->interaction->getRequest()->setBody($this->multipart);
        $this->client
            ->expects($this->exactly(count($this->parts)))
            ->method('call')
            ->willReturnCallback(
                fn (string $method, int $interactionId, int $partId, string $contentType, string $path, string $name, string $boundary) =>
                match([$method, $interactionId, $partId, $contentType, $path, $name, $boundary]) {
                    ['pactffi_with_multipart_file_v2', $this->interactionHandle, $this->requestPartId, $this->parts[0]->getContentType(), $this->parts[0]->getPath(), $this->parts[0]->getName(), $this->boundary] => (object) ['failed' => null],
                    ['pactffi_with_multipart_file_v2', $this->interactionHandle, $this->requestPartId, $this->parts[1]->getContentType(), $this->parts[1]->getPath(), $this->parts[1]->getName(), $this->boundary] => (object) ['failed' => null],
                    ['pactffi_with_multipart_file_v2', $this->interactionHandle, $this->requestPartId, $this->parts[2]->getContentType(), $this->parts[2]->getPath(), $this->parts[2]->getName(), $this->boundary] => (object) (['failed' => $success ? null : $this->failed]),
                }
            );
        if (!$success) {
            $this->expectException(PartNotAddedException::class);
            $this->expectExceptionMessage("Can not add part '{$this->parts[2]->getName()}': {$this->message}");
        }
        $this->driver->registerBody($this->interaction, InteractionPart::REQUEST);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testResponseMultipartBody(bool $success): void
    {
        $this->interaction->getResponse()->setBody($this->multipart);
        $this->client
            ->expects($this->exactly(count($this->parts)))
            ->method('call')
            ->willReturnCallback(
                fn (string $method, int $interactionId, int $partId, string $contentType, string $path, string $name, string $boundary) =>
                match([$method, $interactionId, $partId, $contentType, $path, $name, $boundary]) {
                    ['pactffi_with_multipart_file_v2', $this->interactionHandle, $this->responsePartId, $this->parts[0]->getContentType(), $this->parts[0]->getPath(), $this->parts[0]->getName(), $this->boundary] => (object) ['failed' => null],
                    ['pactffi_with_multipart_file_v2', $this->interactionHandle, $this->responsePartId, $this->parts[1]->getContentType(), $this->parts[1]->getPath(), $this->parts[1]->getName(), $this->boundary] => (object) ['failed' => null],
                    ['pactffi_with_multipart_file_v2', $this->interactionHandle, $this->responsePartId, $this->parts[2]->getContentType(), $this->parts[2]->getPath(), $this->parts[2]->getName(), $this->boundary] => (object) (['failed' => $success ? null : $this->failed]),
                }
            );
        if (!$success) {
            $this->expectException(PartNotAddedException::class);
            $this->expectExceptionMessage("Can not add part '{$this->parts[2]->getName()}': {$this->message}");
        }
        $this->driver->registerBody($this->interaction, InteractionPart::RESPONSE);
    }

    #[TestWith([InteractionPart::REQUEST])]
    #[TestWith([InteractionPart::RESPONSE])]
    public function testEmptyBody(InteractionPart $part): void
    {
        $this->client
            ->expects($this->never())
            ->method('call');
        $this->driver->registerBody($this->interaction, $part);
    }
}
