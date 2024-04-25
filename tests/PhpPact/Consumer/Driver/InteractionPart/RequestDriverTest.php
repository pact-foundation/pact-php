<?php

namespace PhpPactTest\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Body\InteractionBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriver;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriverInterface;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\FFI\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RequestDriverTest extends TestCase
{
    private RequestDriverInterface $driver;
    private ClientInterface&MockObject $client;
    private InteractionBodyDriverInterface&MockObject $bodyDriver;
    private Interaction $interaction;
    private int $requestPartId = 1;
    private int $interactionHandle = 123;
    private string $method = 'POST';
    private string $path = '/items/item';
    private array $query = [
        'query1' => ['query-value-1', 'query-value-2'],
        'query2' => ['query-value-3'],
    ];
    private array $headers = [
        'header1' => ['header-value-1'],
        'header2' => ['header-value-2', 'header-value-3'],
    ];

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->bodyDriver = $this->createMock(InteractionBodyDriverInterface::class);
        $this->driver = new RequestDriver($this->client, $this->bodyDriver);
        $this->interaction = new Interaction();
        $this->interaction->setHandle($this->interactionHandle);
        $request = new ConsumerRequest();
        $request->setMethod($this->method);
        $request->setPath($this->path);
        $request->setQuery($this->query);
        $request->setHeaders($this->headers);
        $this->interaction->setRequest($request);
    }

    public function testRegisterRequest(): void
    {
        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('InteractionPart_Request')
            ->willReturn($this->requestPartId);
        $this->client
            ->expects($this->exactly(7))
            ->method('call')
            ->willReturnCallback(
                fn (...$args) => match($args) {
                    ['pactffi_with_request', $this->interactionHandle, $this->method, $this->path] => null,
                    ['pactffi_with_query_parameter_v2', $this->interactionHandle, 'query1', 0, 'query-value-1'] => null,
                    ['pactffi_with_query_parameter_v2', $this->interactionHandle, 'query1', 1, 'query-value-2'] => null,
                    ['pactffi_with_query_parameter_v2', $this->interactionHandle, 'query2', 0, 'query-value-3'] => null,
                    ['pactffi_with_header_v2', $this->interactionHandle, $this->requestPartId, 'header1', 0, 'header-value-1'] => null,
                    ['pactffi_with_header_v2', $this->interactionHandle, $this->requestPartId, 'header2', 0, 'header-value-2'] => null,
                    ['pactffi_with_header_v2', $this->interactionHandle, $this->requestPartId, 'header2', 1, 'header-value-3'] => null,
                }
            );
        $this->bodyDriver
            ->expects($this->once())
            ->method('registerBody')
            ->with($this->interaction, InteractionPart::REQUEST);
        $this->driver->registerRequest($this->interaction);
    }
}
