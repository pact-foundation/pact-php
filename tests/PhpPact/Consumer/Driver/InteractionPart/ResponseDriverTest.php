<?php

namespace PhpPactTest\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Body\InteractionBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriver;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriverInterface;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\FFI\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ResponseDriverTest extends TestCase
{
    private ResponseDriverInterface $driver;
    private ClientInterface&MockObject $client;
    private InteractionBodyDriverInterface&MockObject $bodyDriver;
    private Interaction $interaction;
    private int $responsePartId = 2;
    private int $interactionHandle = 123;
    private string $status = '400';
    private array $headers = [
        'header1' => ['header-value-1'],
        'header2' => ['header-value-2', 'header-value-3'],
    ];

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->bodyDriver = $this->createMock(InteractionBodyDriverInterface::class);
        $this->driver = new ResponseDriver($this->client, $this->bodyDriver);
        $this->interaction = new Interaction();
        $this->interaction->setHandle($this->interactionHandle);
        $response = new ProviderResponse();
        $response->setStatus($this->status);
        $response->setHeaders($this->headers);
        $this->interaction->setResponse($response);
    }

    public function testRegisterResponse(): void
    {
        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('InteractionPart_Response')
            ->willReturn($this->responsePartId);
        $this->client
            ->expects($this->exactly(4))
            ->method('call')
            ->willReturnCallback(
                fn (...$args) => match($args) {
                    ['pactffi_response_status_v2', $this->interactionHandle, $this->status] => null,
                    ['pactffi_with_header_v2', $this->interactionHandle, $this->responsePartId, 'header1', 0, 'header-value-1'] => null,
                    ['pactffi_with_header_v2', $this->interactionHandle, $this->responsePartId, 'header2', 0, 'header-value-2'] => null,
                    ['pactffi_with_header_v2', $this->interactionHandle, $this->responsePartId, 'header2', 1, 'header-value-3'] => null,
                }
            );
        $this->bodyDriver
            ->expects($this->once())
            ->method('registerBody')
            ->with($this->interaction, InteractionPart::RESPONSE);
        $this->driver->registerResponse($this->interaction);
    }
}
