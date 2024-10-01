<?php

namespace PhpPactTest\Consumer;

use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Factory\InteractionDriverFactoryInterface;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Consumer\Model\ProviderState;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Standalone\MockService\Model\VerifyResult;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class InteractionBuilderTest extends TestCase
{
    private InteractionBuilder $builder;
    private InteractionDriverInterface&MockObject $driver;
    private MockServerConfigInterface&MockObject $config;
    private InteractionDriverFactoryInterface&MockObject $driverFactory;

    public function setUp(): void
    {
        $this->driver = $this->createMock(InteractionDriverInterface::class);
        $this->config = $this->createMock(MockServerConfigInterface::class);
        $this->driverFactory = $this->createMock(InteractionDriverFactoryInterface::class);
        $this->driverFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->config)
            ->willReturn($this->driver);
        $this->builder = new InteractionBuilder($this->config, $this->driverFactory);
    }

    public function testNewInteraction(): void
    {
        $oldInteraction = $this->getInteraction();
        $this->builder->newInteraction();
        $newInteraction = $this->getInteraction();
        $this->assertNotSame($oldInteraction, $newInteraction);
    }

    public function testGiven(): void
    {
        $this->assertSame($this->builder, $this->builder->given('test', ['key' => 'value']));
        $interaction = $this->getInteraction();
        $providerStates = $interaction->getProviderStates();
        $this->assertCount(1, $providerStates);
        $providerState = $providerStates[0];
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('test', $providerState->getName());
        $this->assertSame(['key' => 'value'], $providerState->getParams());
    }

    public function testUponReceiving(): void
    {
        $description = 'interaction description';
        $this->assertSame($this->builder, $this->builder->uponReceiving($description));
        $interaction = $this->getInteraction();
        $this->assertSame($description, $interaction->getDescription());
    }

    public function testWithRequest(): void
    {
        $request = new ConsumerRequest();
        $this->assertSame($this->builder, $this->builder->with($request));
        $interaction = $this->getInteraction();
        $this->assertSame($request, $interaction->getRequest());
    }

    #[TestWith([false, true])]
    #[TestWith([true, true])]
    #[TestWith([false, false])]
    #[TestWith([true, false])]
    public function testWillRespondWith(bool $startMockServer, bool $result): void
    {
        $response = new ProviderResponse();
        $interaction = $this->getInteraction();
        $this->driver
            ->expects($this->once())
            ->method('registerInteraction')
            ->with($interaction, $startMockServer)
            ->willReturn($result);
        $this->assertSame($result, $this->builder->willRespondWith($response, $startMockServer));
        $this->assertSame($response, $interaction->getResponse());
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testVerify(bool $matched): void
    {
        $this->driver
            ->expects($this->once())
            ->method('verifyInteractions')
            ->willReturn(new VerifyResult($matched, ''));
        $this->assertSame($matched, $this->builder->verify());
    }

    #[TestWith([null])]
    #[TestWith(['key'])]
    public function testSetKey(?string $key): void
    {
        $this->assertSame($this->builder, $this->builder->key($key));
        $interaction = $this->getInteraction();
        $this->assertSame($key, $interaction->getKey());
    }

    #[TestWith([null])]
    #[TestWith([false])]
    #[TestWith([true])]
    public function testSetPending(?bool $pending): void
    {
        $this->assertSame($this->builder, $this->builder->pending($pending));
        $interaction = $this->getInteraction();
        $this->assertSame($pending, $interaction->getPending());
    }

    /**
     * @param array<string, mixed> $comments
     */
    #[TestWith([[]])]
    #[TestWith([['key1' => null, 'key2' => 'value', 'key3' => ['value']]])]
    public function testSetComments(array $comments): void
    {
        $this->assertSame($this->builder, $this->builder->comments($comments));
        $interaction = $this->getInteraction();
        $this->assertSame($comments, $interaction->getComments());
    }

    public function testAddTextComment(): void
    {
        $this->assertSame($this->builder, $this->builder->comment('comment 1'));
        $this->assertSame($this->builder, $this->builder->comment('comment 2'));
        $interaction = $this->getInteraction();
        $this->assertSame(['comment 1', 'comment 2'], $interaction->getTextComments());
    }

    private function getInteraction(): Interaction
    {
        $reflection = new ReflectionProperty($this->builder, 'interaction');
        $interaction = $reflection->getValue($this->builder);
        $this->assertInstanceOf(Interaction::class, $interaction);

        return $interaction;
    }
}
