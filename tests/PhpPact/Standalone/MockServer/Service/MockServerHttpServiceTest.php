<?php

namespace PhpPact\Standalone\MockService\Service;

use GuzzleHttp\Exception\ServerException;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Exception\ConnectionException;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Standalone\MockService\MockServer;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;
use stdClass;

class MockServerHttpServiceTest extends TestCase
{
    /** @var MockServerHttpServiceInterface */
    private $service;

    /** @var MockServer */
    private $mockServer;

    /** @var MockServerConfigInterface */
    private $config;

    /**
     * @throws MissingEnvVariableException
     * @throws \Exception
     */
    protected function setUp()
    {
        $this->config     = new MockServerEnvConfig();
        $this->mockServer = new MockServer($this->config);
        $this->mockServer->start();
        $this->service = new MockServerHttpService(new GuzzleClient(), $this->config);
    }

    protected function tearDown()
    {
        $this->mockServer->stop();
    }

    /**
     * @throws ConnectionException
     */
    public function testHealthCheck()
    {
        $result = $this->service->healthCheck();
        $this->assertTrue($result);
    }

    public function testRegisterInteraction()
    {
        $request = new ConsumerRequest();
        $request
            ->setPath('/example')
            ->setMethod('GET');
        $response = new ProviderResponse();
        $response->setStatus(200);

        $interaction = new Interaction();
        $interaction
            ->setDescription('Fake description')
            ->setProviderState('Fake provider state')
            ->setRequest($request)
            ->setResponse($response);

        $result = $this->service->registerInteraction($interaction);

        $this->assertTrue($result);
    }

    public function testDeleteAllInteractions()
    {
        $result = $this->service->deleteAllInteractions();
        $this->assertTrue($result);
    }

    public function testVerifyInteractions()
    {
        $result = $this->service->verifyInteractions();
        $this->assertTrue($result);
    }

    public function testVerifyInteractionsFailure()
    {
        $request = new ConsumerRequest();
        $request
            ->setPath('/example')
            ->setMethod('GET');

        $response = new ProviderResponse();
        $response->setStatus(200);

        $interaction = new Interaction();
        $interaction
            ->setDescription('Some description')
            ->setProviderState('Some state')
            ->setRequest($request)
            ->setResponse($response);
        $this->service->registerInteraction($interaction);

        $this->expectException(ServerException::class);
        $result = $this->service->verifyInteractions();
        $this->assertFalse($result);
    }

    public function testGetPactJson()
    {
        $result = $this->service->getPactJson();
        $this->assertEquals('{"consumer":{"name":"someConsumer"},"provider":{"name":"someProvider"},"interactions":[],"metadata":{"pactSpecification":{"version":"2.0.0"}}}', $result);
    }

    public function testFullGetInteraction()
    {
        $request = new ConsumerRequest();
        $request
            ->setPath('/example')
            ->setMethod('GET')
            ->setQuery('enabled=true')
            ->addQueryParameter('order', 'asc')
            ->addQueryParameter('value', '12')
            ->addHeader('Content-Type', 'application/json');

        $expectedResponseBody = [
            'message' => 'Hello, world!',
        ];
        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->setBody($expectedResponseBody)
            ->addHeader('Content-Type', 'application/json');

        $interaction = new Interaction();
        $interaction
            ->setDescription('Fake description')
            ->setProviderState('Fake provider state')
            ->setRequest($request)
            ->setResponse($response);

        $result = $this->service->registerInteraction($interaction);

        $this->assertTrue($result);

        $client   = new GuzzleClient();
        $uri      = $this->config->getBaseUri()->withPath('/example')->withQuery('enabled=true&order=asc&value=12');
        $response = $client->get($uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $body = $response->getBody()->getContents();
        $this->assertEquals(\json_encode($expectedResponseBody), $body);
        $this->assertEquals($response->getHeaderLine('Access-Control-Allow-Origin'), '*', 'CORS flag not set properly');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws MissingEnvVariableException
     * @throws \Exception
     */
    public function testMatcherWithMockServer()
    {
        $matcher = new Matcher();

        $category       = new stdClass();
        $category->name = $matcher->term('Games', '[gbBG]');

        $request = new ConsumerRequest();
        $request
            ->setPath('/test')
            ->setMethod('GET');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'results' => $matcher->eachLike($category),
            ]);

        $config      = new MockServerEnvConfig();
        $interaction = new InteractionBuilder($config);
        $interaction
            ->given('Something')
            ->uponReceiving('Stuff')
            ->with($request)
            ->willRespondWith($response);

        $client = new GuzzleClient();
        $uri    = $this->config->getBaseUri()->withPath('/test');
        $client->get($uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $httpClient = new MockServerHttpService(new GuzzleClient(), $config);

        $pact = \json_decode($httpClient->getPactJson(), true);

        $this->assertArrayHasKey('$.body.results[*].name', $pact['interactions'][0]['response']['matchingRules']);
    }
}
