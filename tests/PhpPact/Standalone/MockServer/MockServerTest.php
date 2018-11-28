<?php

namespace PhpPact\Consumer;

use GuzzleHttp\Exception\ConnectException;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Exception\HealthCheckFailedException;
use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Standalone\MockService\MockServer;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PhpPact\Standalone\MockService\Service\MockServerHttpService;
use PHPUnit\Framework\TestCase;

class MockServerTest extends TestCase
{
    /**
     * @throws MissingEnvVariableException
     * @throws \Exception
     */
    public function testStartAndStop()
    {
        try {
            $mockServer = new MockServer(new MockServerEnvConfig());
            $pid        = $mockServer->start();
            $this->assertTrue(\is_int($pid));
        } finally {
            $result = $mockServer->stop();
            $this->assertTrue($result);
        }
    }

    /**
     * @throws MissingEnvVariableException
     * @throws \Exception
     */
    public function testStartAndStopWithRecognizedTimeout()
    {
        // the mock server actually takes more than one second to be ready
        // we use this fact to test the timeout
        $orig = \getenv('PACT_MOCK_SERVER_HEALTH_CHECK_TIMEOUT');
        \putenv('PACT_MOCK_SERVER_HEALTH_CHECK_TIMEOUT=1');

        $httpService = $this->getMockBuilder(MockServerHttpService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionException = $this->getMockBuilder(ConnectException::class)
            ->disableOriginalConstructor()
            ->getMock();

        // take sth lower than the default value
        $httpService->expects($this->atMost(5))
            ->method('healthCheck')
            ->will($this->returnCallback(function () use ($connectionException) {
                throw $connectionException;
            }));

        try {
            $mockServer = new MockServer(new MockServerEnvConfig(), $httpService);
            $mockServer->start();
            $this->fail('MockServer should not pass defined health check.');
        } catch (HealthCheckFailedException $e) {
            $this->assertTrue(true);
        } finally {
            $mockServer->stop();
            \putenv('PACT_MOCK_SERVER_HEALTH_CHECK_TIMEOUT=' . $orig);
        }
    }

    /**
     *	Test that the mock server writes to the appropriate location if getPactJson is not called
     */
    public function testFileWrittenWithGracefulExit()
    {
        // build and start the mock server
        $mockServerConfig = new MockServerEnvConfig();
        $mockServerConfig->setConsumer('gracefultest');
        $mockServerConfig->setPort('7203');

        // delete original file if it exists
        $filePath = $mockServerConfig->getPactDir() . \strtolower($mockServerConfig->getConsumer()) . '-' . \strtolower($mockServerConfig->getProvider()) . '.json';
        if (\file_exists($filePath)) {
            \unlink($filePath);
        }

        // start the server
        $mockServer = new MockServer($mockServerConfig);
        $pid        = $mockServer->start();

        // build the expected consumer request / provider response
        $path    = '/gracefullyWrite';
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath($path)
            ->addHeader('Content-Type', 'application/json');

        $body         = new \stdClass();
        $body->results= 'write me';

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody($body);

        // build up the expected results and appropriate responses
        $mockService = new InteractionBuilder($mockServerConfig);
        $mockService->given('Graceful Test')
            ->uponReceiving('A GET request to return JSON')
            ->with($request)
            ->willRespondWith($response);

        $httpClient = new GuzzleClient();
        $uri        = $mockServerConfig->getBaseUri();
        $uri        = $uri->withPath($path);
        $httpResponse   = $httpClient->get($uri, [
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $mockService->verify();

        // run the veritfy interactions
        $httpService = new MockServerHttpService(new GuzzleClient(), $mockServerConfig);
        $httpService->verifyInteractions();
        
        $result = $mockServer->stop();
        $this->assertTrue($result);
        
        $this->assertTrue(\file_exists($filePath), 'expect the pact to have been written without calling getPactJson');
        
       
    }
}
