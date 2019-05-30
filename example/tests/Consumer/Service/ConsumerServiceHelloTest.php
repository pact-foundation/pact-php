<?php

namespace Consumer\Service;

use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class ConsumerServiceHelloTest extends TestCase
{
    /**
     * Example PACT test.
     *
     * @throws \Exception
     */
    public function testGetHelloString()
    {
        $matcher = new Matcher();

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
                'message' => $matcher->term('Hello, Bob', '(Hello, )[A-Za-z]')
            ]);

        // Create a configuration that reflects the server that was started. You can create a custom MockServerConfigInterface if needed.
        $config  = new MockServerEnvConfig();
        $builder = new InteractionBuilder($config);
        $builder
            ->uponReceiving('A get request to /hello/{name}')
            ->with($request)
            ->willRespondWith($response); // This has to be last. This is what makes an API request to the Mock Server to set the interaction.

        $service = new HttpClientService($config->getBaseUri()); // Pass in the URL to the Mock Server.
        $result  = $service->getHelloString('Bob'); // Make the real API request against the Mock Server.

        $builder->verify(); // This will verify that the interactions took place.

        $this->assertEquals('Hello, Bob', $result); // Make your assertions.
    }
}
