<?php

namespace PhpPact\Consumer;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Consumer\Service\MockServerHttpService;
use PhpPact\Core\BinaryManager\BinaryManager;
use PhpPact\Core\Broker\Service\HttpService;
use PhpPact\Core\Http\GuzzleClient;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

/**
 * PACT listener that can be used with environment variables and easily attached to PHPUnit configuration.
 * Class PactTestListener
 */
class PactTestListener implements TestListener
{
    use TestListenerDefaultImplementation;

    /** @var MockServer */
    private $server;

    /**
     * Name of the test suite configured in your phpunit config.
     *
     * @var string
     */
    private $testSuiteNames;

    /** @var MockServerConfigInterface */
    private $mockServerConfig;

    /**
     * PactTestListener constructor.
     *
     * @param string[] $testSuiteNames test Suite names that need evaluated with the listener
     */
    public function __construct(array $testSuiteNames)
    {
        $this->testSuiteNames   = $testSuiteNames;
        $this->mockServerConfig = new MockServerEnvConfig();
    }

    /**
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite)
    {
        if (\in_array($suite->getName(), $this->testSuiteNames)) {
            $this->server = new MockServer($this->mockServerConfig, new BinaryManager());
            $this->server->start();
        }
    }

    /**
     * Publish JSON results to PACT Broker and stop the Mock Server.
     *
     * @param TestSuite $suite
     *
     * @throws \Exception
     */
    public function endTestSuite(TestSuite $suite)
    {
        if (\in_array($suite->getName(), $this->testSuiteNames)) {
            try {
                $httpService = new MockServerHttpService(new GuzzleClient(), $this->mockServerConfig);
                $httpService->verifyInteractions();

                $json = $httpService->getPactJson();
            } finally {
                $this->server->stop();
            }

            $brokerHttpService = new HttpService(new GuzzleClient(), new Uri(\getenv('PACT_BROKER_URI')));
            $brokerHttpService->publishJson($json, \getenv('PACT_CONSUMER_VERSION'));
        }
    }
}
