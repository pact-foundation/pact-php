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
    /**
     * Example PACT test.
     */
    public function testGetHelloString()
    {
        // Create your expected request from the consumer.
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/hello/Bob')
            ->addHeader('Content-Type', 'application/json');

        // Create your expected response from the provider.
        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'message' => new RegexMatcher('Hello, Bob', '(Hello, )[A-Za-z]')
            ]);

        // Create a configuration that reflects the server that was started. You can create a custom MockServerConfigInterface if needed.
        $config      = new MockServerEnvConfig();
        $mockService = new InteractionBuilder($config);
        $mockService
            ->given('Get Hello')
            ->uponReceiving('A get request to /hello/{name}')
            ->with($request)
            ->willRespondWith($response); // This has to be last. This is what makes an API request to the Mock Server to set the interaction.

        $service = new HttpService($config->getBaseUri()); // Pass in the URL to the Mock Server.
        $result  = $service->getHelloString('Bob'); // Make the real API request against the Mock Server.

        $this->assertEquals('Hello, Bob', $result); // Make your assertions.
    }
}
