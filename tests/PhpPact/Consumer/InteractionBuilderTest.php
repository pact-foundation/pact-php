<?php

namespace PhpPact\Consumer;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class InteractionBuilderTest extends TestCase
{
    private InteractionBuilder $builder;
    private string $consumer = 'test-consumer';
    private string $provider = 'test-provider';
    private string $dir      = __DIR__ . '/../../_output';
    private Client $httpClient;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $config = new MockServerConfig();
        $config->setProvider($this->provider);
        $config->setConsumer($this->consumer);
        $config->setPactDir($this->dir);
        $config->setPactSpecificationVersion('3.0.0');
        $this->builder    = new InteractionBuilder($config);
        $this->httpClient = new Client();
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function testMultipleInteractions(): void
    {
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setPath('/something')
            ->setMethod('GET')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->setBody([
                'message' => 'Hello, world!',
                'age'     => $matcher->like(73),
            ])
            ->addHeader('Content-Type', 'application/json');

        // Simple get request.
        $this->builder
            ->newInteraction()
            ->given('A single item.')
            ->uponReceiving('A simple get request.')
            ->with($request)
            ->willRespondWith($response);

        $request = new ConsumerRequest();
        $request
            ->setPath('/something')
            ->setMethod('POST')
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'someStuff'  => 'someOtherStuff',
                'someNumber' => 12,
                'anArray'    => [
                    12,
                    'words here',
                    493.5,
                ],
            ]);

        $response = new ProviderResponse();
        $response
            ->setStatus(201)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'message' => 'Hello, world!',
            ]);

        // Post request with body.
        $this->builder
            ->newInteraction()
            ->given('It does not matter.')
            ->uponReceiving('A post request with body.')
            ->with($request)
            ->willRespondWith($response)
            ->createMockServer();

        $this->httpClient->post(new Uri("{$this->builder->getBaseUri()}/something"), [
            'headers' => ['Content-Type' => 'application/json'],
            'json'    => [
                'someStuff'  => 'someThingElse',
                'someNumber' => 11,
                'anArray'    => [
                    'some words',
                    'some other words here',
                    99.99,
                ],
            ],
            'http_errors' => false,
        ]);

        $this->assertFalse($this->builder->verify());
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function testSingleInteraction(): void
    {
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setPath('/something')
            ->setMethod('GET')
            ->addHeader('Content-Type', 'application/json')
            ->addQueryParameter('test', 1)
            ->addQueryParameter('another[]', 2)
            ->addQueryParameter('another[]', 33);

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'list' => $matcher->eachLike([
                    'test'    => 1,
                    'another' => 2,
                ]),
            ]);

        $this->builder
            ->newInteraction()
            ->given('A list of items.')
            ->uponReceiving('A get request with list in body.')
            ->with($request)
            ->willRespondWith($response)
            ->createMockServer();

        $this->httpClient->get(new Uri("{$this->builder->getBaseUri()}/something?test=1&another[]=2&another[]=33"), [
            'headers'     => ['Content-Type' => 'application/json'],
            'http_errors' => false,
        ]);

        $this->assertTrue($this->builder->verify());
    }

    public function testGetBaseUriWhenMockServerNotStarted(): void
    {
        $this->expectException(MockServerNotStartedException::class);
        $this->expectExceptionMessage('Mock server is not started.');
        $this->builder->getBaseUri();
    }

    public function testVerifyWhenMockServerNotStarted(): void
    {
        $this->expectException(MockServerNotStartedException::class);
        $this->expectExceptionMessage('Mock server is not started.');
        $this->builder->verify();
    }

    public function testGetBaseUriWhenMockServerStopped(): void
    {
        $this->builder->createMockServer();
        $this->builder->verify();
        $this->expectException(MockServerNotStartedException::class);
        $this->expectExceptionMessage('Mock server is not started.');
        $this->builder->getBaseUri();
    }

    public function testVerifyWhenMockServerStopped(): void
    {
        $this->builder->createMockServer();
        $this->builder->verify();
        $this->expectException(MockServerNotStartedException::class);
        $this->expectExceptionMessage('Mock server is not started.');
        $this->builder->verify();
    }
}
