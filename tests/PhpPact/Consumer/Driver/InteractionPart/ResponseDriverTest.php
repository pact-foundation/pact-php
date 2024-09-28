<?php

namespace PhpPactTest\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Body\InteractionBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriver;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriverInterface;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\FFI\ClientInterface;
use PhpPactTest\Helper\FFI\ClientTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ResponseDriverTest extends TestCase
{
    use ClientTrait;

    private ResponseDriverInterface $driver;
    private InteractionBodyDriverInterface&MockObject $bodyDriver;
    private Interaction $interaction;
    private int $responsePartId = 2;
    private int $interactionHandle = 123;
    private int $status = 400;
    /**
     * @var array<string, string[]>
     */
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
            ->method('getInteractionPartResponse')
            ->willReturn($this->responsePartId);
        $this->expectsWithHeaderV2($this->interactionHandle, $this->responsePartId, $this->headers);
        $this->expectsResponseStatusV2($this->interactionHandle, (string) $this->status, true);
        $this->bodyDriver
            ->expects($this->once())
            ->method('registerBody')
            ->with($this->interaction, InteractionPart::RESPONSE);
        $this->driver->registerResponse($this->interaction);
    }
}
