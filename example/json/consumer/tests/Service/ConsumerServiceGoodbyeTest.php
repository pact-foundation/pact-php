<?php

namespace JsonConsumer\Tests\Service;

use JsonConsumer\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
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

        $config = new MockServerConfig();
        $config
            ->setConsumer('jsonConsumer')
            ->setProvider('jsonProvider')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->given('Get Goodbye')
            ->uponReceiving('A get request to /goodbye/{name}')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpClientService($config->getBaseUri());
        $goodbyeResult = $service->getGoodbyeString('Bob');
        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
        $this->assertEquals('Goodbye, Bob', $goodbyeResult);
    }
}
