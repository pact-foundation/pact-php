<?php

namespace PhpPactTest\Consumer\Driver\Interaction;

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
    private MockServerInterface&MockObject $mockServer;
    private PactDriverInterface&MockObject $pactDriver;
    private RequestDriverInterface&MockObject $requestDriver;
    private ResponseDriverInterface&MockObject $responseDriver;
    private Interaction $interaction;
    private int $interactionHandle = 123;
    private int $pactHandle = 234;
    private string $description = 'Sending request receiving response';
    /**
     * @var array<string, array<string, string>>
     */
    private array $providerStates = [
        'item exist' => [
            'id' => '12',
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
        $this->expectsNewInteraction($this->pactHandle, $this->description, $this->interactionHandle);
        $this->expectsGiven($this->interactionHandle, 'item exist', true);
        $this->expectsGivenWithParam($this->interactionHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ], true);
        $this->expectsUponReceiving($this->interactionHandle, $this->description, true);
        $this->mockServer
            ->expects($startMockServer ? $this->once() : $this->never())
            ->method('start');
        $this->assertTrue($this->driver->registerInteraction($this->interaction, $startMockServer));
        $this->assertSame($this->interactionHandle, $this->interaction->getHandle());
    }

    #[TestWith([null, true])]
    #[TestWith([null, true])]
    #[TestWith(['123ABC', false])]
    #[TestWith(['123ABC', true])]
    public function testSetKey(?string $key, bool $success): void
    {
        $this->interaction->setKey($key);
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->expectsNewInteraction($this->pactHandle, $this->description, $this->interactionHandle);
        $this->expectsGiven($this->interactionHandle, 'item exist', true);
        $this->expectsGivenWithParam($this->interactionHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ], true);
        $this->expectsUponReceiving($this->interactionHandle, $this->description, true);
        $this->expectsSetInteractionKey($this->interactionHandle, $this->description, $key, $success);
        $this->driver->registerInteraction($this->interaction, false);
    }

    #[TestWith([null, true])]
    #[TestWith([null, true])]
    #[TestWith([true, false])]
    #[TestWith([true, true])]
    #[TestWith([false, false])]
    #[TestWith([false, true])]
    public function testSetPending(?bool $pending, bool $success): void
    {
        $this->interaction->setPending($pending);
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->expectsNewInteraction($this->pactHandle, $this->description, $this->interactionHandle);
        $this->expectsGiven($this->interactionHandle, 'item exist', true);
        $this->expectsGivenWithParam($this->interactionHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ], true);
        $this->expectsUponReceiving($this->interactionHandle, $this->description, true);
        $this->expectsSetInteractionPending($this->interactionHandle, $this->description, $pending, $success);
        $this->driver->registerInteraction($this->interaction, false);
    }

    /**
     * @param array<string, mixed> $comments
     */
    #[TestWith([[], true])]
    #[TestWith([['key1' => null], true])]
    #[TestWith([['key1' => null], false])]
    #[TestWith([['key2' => 'string value'], true])]
    #[TestWith([['key2' => 'string value'], false])]
    #[TestWith([['key3' => ['value 1', 'value 2']], true])]
    #[TestWith([['key3' => ['value 1', 'value 2']], false])]
    public function testSetComments(array $comments, bool $success): void
    {
        $this->interaction->setComments($comments);
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->expectsNewInteraction($this->pactHandle, $this->description, $this->interactionHandle);
        $this->expectsGiven($this->interactionHandle, 'item exist', true);
        $this->expectsGivenWithParam($this->interactionHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ], true);
        $this->expectsUponReceiving($this->interactionHandle, $this->description, true);
        $this->expectsSetComments($this->interactionHandle, $this->description, $comments, $success);
        $this->driver->registerInteraction($this->interaction, false);
    }

    /**
     * @param string[] $comments
     */
    #[TestWith([['comment 1', 'comment 2'], false])]
    #[TestWith([['comment 1', 'comment 2'], true])]
    public function testAddTextComment(array $comments, bool $success): void
    {
        foreach ($comments as $comment) {
            $this->interaction->addTextComment($comment);
        }
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->expectsNewInteraction($this->pactHandle, $this->description, $this->interactionHandle);
        $this->expectsGiven($this->interactionHandle, 'item exist', true);
        $this->expectsGivenWithParam($this->interactionHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ], true);
        $this->expectsUponReceiving($this->interactionHandle, $this->description, true);
        $this->expectsAddTextComments($this->interactionHandle, $this->description, $comments, $success);
        $this->driver->registerInteraction($this->interaction, false);
    }

    public function testGivenCanNotModifyInteraction(): void
    {
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->expectsNewInteraction($this->pactHandle, $this->description, $this->interactionHandle);
        $this->expectsGiven($this->interactionHandle, 'item exist', false);
        $this->driver->registerInteraction($this->interaction, false);
    }

    public function testGivenWithParamCanNotModifyInteraction(): void
    {
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->expectsNewInteraction($this->pactHandle, $this->description, $this->interactionHandle);
        $this->expectsGiven($this->interactionHandle, 'item exist', true);
        $this->expectsGivenWithParam($this->interactionHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ], false);
        $this->driver->registerInteraction($this->interaction, false);
    }

    public function testUponReceivingCanNotModifyInteraction(): void
    {
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $this->expectsNewInteraction($this->pactHandle, $this->description, $this->interactionHandle);
        $this->expectsGiven($this->interactionHandle, 'item exist', true);
        $this->expectsGivenWithParam($this->interactionHandle, 'item exist', [
            'id' => '12',
            'name' => 'abc',
        ], true);
        $this->expectsUponReceiving($this->interactionHandle, $this->description, false);
        $this->driver->registerInteraction($this->interaction, false);
    }
}
