<?php

namespace PhpPact\Consumer\Service;

use GuzzleHttp\Exception\ServerException;
use PhpPact\Consumer\MockServer;
use PhpPact\Consumer\MockServerConfigInterface;
use PhpPact\Consumer\MockServerEnvConfig;
use PhpPact\Core\BinaryManager\BinaryManager;
use PhpPact\Core\Http\GuzzleClient;
use PhpPact\Core\Model\ConsumerRequest;
use PhpPact\Core\Model\Interaction;
use PhpPact\Core\Model\ProviderResponse;
use PHPUnit\Framework\TestCase;

class MockServerHttpServiceTest extends TestCase
{
    /** @var MockServerHttpServiceInterface */
    private $service;

    /** @var MockServer */
    private $mockServer;

    /** @var MockServerConfigInterface */
    private $config;

    protected function setUp()
    {
        $this->config     = new MockServerEnvConfig();
        $this->mockServer = new MockServer($this->config, new BinaryManager());
        $this->mockServer->start();
        $this->service = new MockServerHttpService(new GuzzleClient(), $this->config);
    }

    protected function tearDown()
    {
        $this->mockServer->stop();
    }

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

        $expectedResponseBody = \json_encode([
            'message' => 'Hello, world!'
        ]);
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
                'Content-Type' => 'application/json'
            ]
        ]);

        $body = $response->getBody()->getContents();
        $this->assertEquals($expectedResponseBody, $body);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
