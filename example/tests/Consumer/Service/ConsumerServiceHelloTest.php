<?php

namespace Consumer\Service;

use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\RegexMatcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockServer\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class ConsumerServiceHelloTest extends TestCase
{
    public function testGetHelloString()
    {
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/hello/Bob')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'message' => new RegexMatcher('Hello, Bob', '(Hello, )[A-Za-z]')
            ]);

        $config      = new MockServerEnvConfig();
        $mockService = new InteractionBuilder($config);
        $mockService
            ->given('Get Hello')
            ->uponReceiving('A get request to /hello/{name}')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpService($config->getBaseUri());
        $result  = $service->getHelloString('Bob');

        $this->assertEquals('Hello, Bob', $result);
    }
}
