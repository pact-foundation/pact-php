<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Consumer\Model\ProviderState;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class InteractionTest extends TestCase
{
    private ConsumerRequest $request;
    private ProviderResponse $response;
    private Interaction $interaction;

    public function setUp(): void
    {
        $this->request = new ConsumerRequest();
        $this->response = new ProviderResponse();
        $this->interaction = new Interaction();
        $this->interaction->setRequest($this->request);
        $this->interaction->setResponse($this->response);
    }

    public function testSetters(): void
    {
        $handle              = 123;
        $description         = 'a message';
        $providerStateName   = 'a provider state';
        $providerStateParams = ['foo' => 'bar'];

        $this->interaction
            ->setHandle($handle)
            ->setDescription($description)
            ->addProviderState($providerStateName, $providerStateParams);

        static::assertSame($handle, $this->interaction->getHandle());
        static::assertSame($description, $this->interaction->getDescription());
        $providerStates = $this->interaction->getProviderStates();
        static::assertCount(1, $providerStates);
        static::assertContainsOnlyInstancesOf(ProviderState::class, $providerStates);
        static::assertEquals($providerStateName, $providerStates[0]->getName());
        static::assertEquals($providerStateParams, $providerStates[0]->getParams());
        static::assertSame($this->request, $this->interaction->getRequest());
        static::assertSame($this->response, $this->interaction->getResponse());
    }

    public function testGetBody(): void
    {
        $requestBody = new Multipart([], 'abc123');
        $this->request->setBody($requestBody);

        $responseBody = new Text('example', 'text/plain');
        $this->response->setBody($responseBody);

        $this->assertSame($requestBody, $this->interaction->getBody(InteractionPart::REQUEST));
        $this->assertSame($responseBody, $this->interaction->getBody(InteractionPart::RESPONSE));
    }

    public function testGetHeaders(): void
    {
        $requestHeaders = ['key1' => ['value1']];
        $this->request->setHeaders($requestHeaders);

        $responseHeaders = ['key2' => ['value1', 'value2']];
        $this->response->setHeaders($responseHeaders);

        $this->assertSame($requestHeaders, $this->interaction->getHeaders(InteractionPart::REQUEST));
        $this->assertSame($responseHeaders, $this->interaction->getHeaders(InteractionPart::RESPONSE));
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testSetProviderState(bool $overwrite): void
    {
        $this->interaction->setProviderState('provider state 1', ['key 1' => 'value 1'], true);
        $providerStates = $this->interaction->setProviderState('provider state 2', ['key 2' => 'value 2'], $overwrite);
        if ($overwrite) {
            $this->assertCount(1, $providerStates);
            $providerState = reset($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 2', $providerState->getName());
            $this->assertSame(['key 2' => 'value 2'], $providerState->getParams());
        } else {
            $this->assertCount(2, $providerStates);
            $providerState = reset($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 1', $providerState->getName());
            $this->assertSame(['key 1' => 'value 1'], $providerState->getParams());
            $providerState = end($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 2', $providerState->getName());
            $this->assertSame(['key 2' => 'value 2'], $providerState->getParams());
        }
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testAddProviderState(bool $overwrite): void
    {
        $this->assertSame($this->interaction, $this->interaction->addProviderState('provider state 1', ['key 1' => 'value 1'], true));
        $this->assertSame($this->interaction, $this->interaction->addProviderState('provider state 2', ['key 2' => 'value 2'], $overwrite));
        $providerStates = $this->interaction->getProviderStates();
        if ($overwrite) {
            $this->assertCount(1, $providerStates);
            $providerState = reset($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 2', $providerState->getName());
            $this->assertSame(['key 2' => 'value 2'], $providerState->getParams());
        } else {
            $this->assertCount(2, $providerStates);
            $providerState = reset($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 1', $providerState->getName());
            $this->assertSame(['key 1' => 'value 1'], $providerState->getParams());
            $providerState = end($providerStates);
            $this->assertInstanceOf(ProviderState::class, $providerState);
            $this->assertSame('provider state 2', $providerState->getName());
            $this->assertSame(['key 2' => 'value 2'], $providerState->getParams());
        }
    }

    public function testAddProviderStateWithParamMixedValue(): void
    {
        $this->interaction->addProviderState('provider state 1', ['string value' => 'test']);
        $this->interaction->addProviderState('provider state 2', ['number value' => 123]);
        $this->interaction->addProviderState('provider state 3', ['array value' => ['value 1', 'value 2', 'value 3']]);
        $this->interaction->addProviderState('provider state 4', ['object value' => (object) ['key 1' => 'value 1', 'key 2' => 'value 2']]);
        $providerStates = $this->interaction->getProviderStates();
        $this->assertCount(4, $providerStates);
        $providerState = reset($providerStates);
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('provider state 1', $providerState->getName());
        $this->assertSame(['string value' => 'test'], $providerState->getParams());
        $providerState = next($providerStates);
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('provider state 2', $providerState->getName());
        $this->assertSame(['number value' => '123'], $providerState->getParams());
        $providerState = next($providerStates);
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('provider state 3', $providerState->getName());
        $this->assertSame(['array value' => '["value 1","value 2","value 3"]'], $providerState->getParams());
        $providerState = next($providerStates);
        $this->assertInstanceOf(ProviderState::class, $providerState);
        $this->assertSame('provider state 4', $providerState->getName());
        $this->assertSame(['object value' => '{"key 1":"value 1","key 2":"value 2"}'], $providerState->getParams());
    }
}
