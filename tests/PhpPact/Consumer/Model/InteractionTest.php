<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Consumer\Model\ProviderState;
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
}
