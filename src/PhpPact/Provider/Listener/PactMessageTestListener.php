<?php

namespace PhpPact\Provider\Listener;

use PhpPact\Provider\Proxy\ProxyServerConfig;
use PhpPact\Provider\Proxy\HttpServer;
use PhpPact\Provider\Proxy\ProxyServerEnvConfig;
use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

/**
 * PACT listener that can be used with environment variables and easily attached to PHPUnit configuration.
 * Class PactTestListener
 */
class PactMessageTestListener implements TestListener
{
    use TestListenerDefaultImplementation;

    /**
     * Name of the test suite configured in your phpunit config.
     *
     * @var string
     */
    private $testSuiteNames;

    /** @var HttpServer */
    private $server;

    /** @var ProxyServerConfig */
    private $config;

    /** @var bool */
    private $failed;

    /**
     * PactTestListener constructor.
     *
     * @param string[] $testSuiteNames test suite names that need evaluated with the listener
     *
     * @throws MissingEnvVariableException
     */
    public function __construct(array $testSuiteNames)
    {
        $this->testSuiteNames   = $testSuiteNames;
        $this->config = new ProxyServerEnvConfig();
    }

    /**
     * @param TestSuite $suite
     *
     * @throws \Exception
     */
    public function startTestSuite(TestSuite $suite): void
    {
        if (\in_array($suite->getName(), $this->testSuiteNames)) {
            $this->server = new HttpServer($this->config);
            $this->server->start();
        }
    }

    public function addError(Test $test, \Throwable $t, float $time): void
    {
        $this->failed = true;
    }

    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        $this->failed = true;
    }

    /**
     * Publish JSON results to PACT Broker and stop the Mock Server.
     *
     * @param TestSuite $suite
     */
    public function endTestSuite(TestSuite $suite): void
    {
        if (\in_array($suite->getName(), $this->testSuiteNames)) {
            $this->server->stop();
        }
    }
}
