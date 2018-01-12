<?php

namespace Consumer\Service;

use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\MockServerEnvConfig;
use PhpPact\Core\Model\ConsumerRequest;
use PhpPact\Core\Model\ProviderResponse;
use PHPUnit\Framework\TestCase;

class ConsumerServiceGoodbyeTest extends TestCase
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

        $config      = new MockServerEnvConfig();
        $mockService = new InteractionBuilder($config);
        $mockService
            ->given('Get Goodbye')
            ->uponReceiving('A get request to /goodbye/{name}')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpService($config->getBaseUri());
        $result  = $service->getGoodbyeString('Bob');

        $this->assertEquals('Goodbye, Bob', $result);
    }
}
