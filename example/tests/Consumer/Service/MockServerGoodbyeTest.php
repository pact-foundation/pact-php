<?php

namespace Consumer\Service;

use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Consumer\TestCase\MockServerTestCase;

class MockServerGoodbyeTest extends MockServerTestCase
{
    public function testGetGoodbyeString()
    {
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/goodbye/Bob')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'message' => 'Goodbye, Bob'
            ]);

        $mockService = new InteractionBuilder($this->config);
        $mockService
            ->given('Get Goodbye')
            ->uponReceiving('A get request to /goodbye/{name}')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpService($this->config->getBaseUri());
        $result  = $service->getGoodbyeString('Bob');

        $this->assertEquals('Goodbye, Bob', $result);
    }
}
