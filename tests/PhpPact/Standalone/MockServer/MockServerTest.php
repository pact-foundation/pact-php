<?php

namespace PhpPactTest\Standalone\MockServer;

use GuzzleHttp\Exception\ConnectException;
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
}
