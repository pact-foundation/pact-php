<?php

namespace Consumer\Service;

use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
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
            ->addHeader('Content-Type', 'application/json;charset=utf-8')
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
