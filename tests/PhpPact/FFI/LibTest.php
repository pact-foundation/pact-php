<?php

namespace PhpPactTest\FFI;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\FFI\ClientInterface;
use PhpPact\FFI\Lib;
use PhpPact\FFI\LibInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LibTest extends TestCase
{
    private ClientInterface|MockObject $client;
    private LibInterface $lib;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->lib = new Lib($this->client);
    }

    public function testGetInteractionPartId(): void
    {
        $requestPartId = 1;
        $responsePartId = 2;
        $this->client
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['InteractionPart_Request', $requestPartId],
                ['InteractionPart_Response', $responsePartId],
            ]);

        $this->assertSame($requestPartId, $this->lib->getInteractionPartId(InteractionPart::REQUEST));
        $this->assertSame($responsePartId, $this->lib->getInteractionPartId(InteractionPart::RESPONSE));
    }
}
