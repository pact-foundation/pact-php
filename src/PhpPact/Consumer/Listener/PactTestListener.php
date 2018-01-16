<?php

namespace PhpPact\Consumer\Listener;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Broker\Service\BrokerHttpService;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\MockServer\MockServer;
use PhpPact\Standalone\MockServer\MockServerConfigInterface;
use PhpPact\Standalone\MockServer\MockServerEnvConfig;
use PhpPact\Standalone\MockServer\Service\MockServerHttpService;
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
     * @param string[] $testSuiteNames test suite names that need evaluated with the listener
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
            $this->server = new MockServer($this->mockServerConfig, new InstallManager());
            $this->server->start();
        }
    }

    /**
     * Publish JSON results to PACT Broker and stop the Mock Server.
     *
     * @param TestSuite $suite
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

            $brokerHttpService = new BrokerHttpService(new GuzzleClient(), new Uri(\getenv('PACT_BROKER_URI')));
            $brokerHttpService->publishJson($json, \getenv('PACT_CONSUMER_VERSION'));
        }
    }
}
