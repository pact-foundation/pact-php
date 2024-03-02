<?php

namespace PhpPactTest\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Exception\InteractionKeyNotSetException;
use PhpPact\Consumer\Driver\Interaction\InteractionDriver;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Standalone\MockService\Model\VerifyResult;
use PhpPactTest\Helper\FFI\ClientTrait;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InteractionDriverTest extends TestCase
{
    use ClientTrait;

    private InteractionDriverInterface $driver;
    private MockServerInterface|MockObject $mockServer;
    private PactDriverInterface|MockObject $pactDriver;
    private RequestDriverInterface|MockObject $requestDriver;
    private ResponseDriverInterface|MockObject $responseDriver;
    private Interaction $interaction;
    private int $interactionHandle = 123;
    private int $pactHandle = 234;
    private string $description = 'Sending request receiving response';
    private array $providerStates = [
        'item exist' => [
            'id' => 12,
            'name' => 'abc',
        ]
    ];

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->mockServer = $this->createMock(MockServerInterface::class);
        $this->pactDriver = $this->createMock(PactDriverInterface::class);
        $this->requestDriver = $this->createMock(RequestDriverInterface::class);
        $this->responseDriver = $this->createMock(ResponseDriverInterface::class);
        $this->driver = new InteractionDriver($this->client, $this->mockServer, $this->pactDriver, $this->requestDriver, $this->responseDriver);
        $this->interaction = new Interaction();
        $this->interaction->setDescription($this->description);
        foreach ($this->providerStates as $name => $params) {
            $this->interaction->addProviderState($name, $params);
        }
    }

    public function testVerifyInteractions(): void
    {
        $result = new VerifyResult(true, '');
        $this->mockServer
            ->expects($this->once())
            ->method('verify')
            ->willReturn($result);
        $this->assertSame($result, $this->driver->verifyInteractions());
    }

    public function testWritePactAndCleanUp(): void
    {
        $this->mockServer
            ->expects($this->once())
            ->method('writePact');
        $this->mockServer
            ->expects($this->once())
            ->method('cleanUp');
        $this->driver->writePactAndCleanUp();
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testRegisterInteraction(bool $startMockServer): void
    {
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->requestDriver
            ->expects($this->once())
            ->method('registerRequest')
            ->with($this->interaction);
        $this->responseDriver
            ->expects($this->once())
            ->method('registerResponse')
            ->with($this->interaction);
        $calls = [
            ['pactffi_new_interaction', $this->pactHandle, $this->description, $this->interactionHandle],
            ['pactffi_given', $this->interactionHandle, 'item exist', null],
            ['pactffi_given_with_param', $this->interactionHandle, 'item exist', 'id', '12', null],
            ['pactffi_given_with_param', $this->interactionHandle, 'item exist', 'name', 'abc', null],
            ['pactffi_upon_receiving', $this->interactionHandle, $this->description, null],
        ];
        $this->assertClientCalls($calls);
        $this->mockServer
            ->expects($this->exactly($startMockServer))
            ->method('start');
        $this->assertTrue($this->driver->registerInteraction($this->interaction, $startMockServer));
        $this->assertSame($this->interactionHandle, $this->interaction->getHandle());
    }

    #[TestWith([null, true])]
    #[TestWith([null, true])]
    #[TestWith(['123ABC', false])]
    #[TestWith(['123ABC', true])]
    public function testSetKey(?string $key, $success): void
    {
        $this->interaction->setKey($key);
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $calls = [
            ['pactffi_new_interaction', $this->pactHandle, $this->description, $this->interactionHandle],
            ['pactffi_given', $this->interactionHandle, 'item exist', null],
            ['pactffi_given_with_param', $this->interactionHandle, 'item exist', 'id', '12', null],
            ['pactffi_given_with_param', $this->interactionHandle, 'item exist', 'name', 'abc', null],
            ['pactffi_upon_receiving', $this->interactionHandle, $this->description, null],
        ];
        if (is_string($key)) {
            $calls[] = ['pactffi_set_key', $this->interactionHandle, $key, $success];
        }
        if (!$success) {
            $this->expectException(InteractionKeyNotSetException::class);
            $this->expectExceptionMessage("Can not set the key '$key' for the interaction '{$this->description}'");
        }
        $this->assertClientCalls($calls);
        $this->driver->registerInteraction($this->interaction, false);
    }
}
