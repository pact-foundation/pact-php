<?php

namespace PhpPact\Consumer\Listener;

use Exception;
use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\Broker\Broker;
use PhpPact\Standalone\Broker\BrokerConfig;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
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

    /**
     * Name of the test suite configured in your phpunit config.
     *
     * @var string[]
     */
    private array $testSuiteNames;

    private MockServerEnvConfig $builderConfig;
    private bool $failed = false;

    /**
     * PactTestListener constructor.
     *
     * @param string[] $testSuiteNames test suite names that need evaluated with the listener
     */
    public function __construct(array $testSuiteNames)
    {
        $this->testSuiteNames = $testSuiteNames;
        $this->builderConfig  = new MockServerEnvConfig();
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
     *
     * @throws Exception
     */
    public function endTestSuite(TestSuite $suite): void
    {
        if (\in_array($suite->getName(), $this->testSuiteNames)) {
            if ($this->failed === true) {
                print 'A unit test has failed. Skipping PACT file upload.';
            } elseif (!($pactBrokerUri = \getenv('PACT_BROKER_URI'))) {
                print 'PACT_BROKER_URI environment variable was not set. Skipping PACT file upload.';
            } elseif (!($consumerVersion = \getenv('PACT_CONSUMER_VERSION'))) {
                print 'PACT_CONSUMER_VERSION environment variable was not set. Skipping PACT file upload.';
            } elseif (!($tag = \getenv('PACT_CONSUMER_TAG'))) {
                print 'PACT_CONSUMER_TAG environment variable was not set. Skipping PACT file upload.';
            } else {
                $brokerConfig = new BrokerConfig();
                $brokerConfig->setPacticipant($this->builderConfig->getConsumer());
                $brokerConfig->setPactLocations($this->builderConfig->getPactDir());
                $brokerConfig->setBrokerUri(new Uri($pactBrokerUri));
                $brokerConfig->setConsumerVersion($consumerVersion);
                $brokerConfig->setTag($tag);
                if (($user = \getenv('PACT_BROKER_HTTP_AUTH_USER')) &&
                    ($pass = \getenv('PACT_BROKER_HTTP_AUTH_PASS'))
                ) {
                    $brokerConfig->setBrokerUsername($user);
                    $brokerConfig->setBrokerPassword($pass);
                }

                if ($bearerToken = \getenv('PACT_BROKER_BEARER_TOKEN')) {
                    $brokerConfig->setBrokerToken($bearerToken);
                }

                $broker = new Broker($brokerConfig);
                $broker->createVersionTag();
                $broker->publish();
                print 'Pact file has been uploaded to the Broker successfully.';
            }
        }
    }
}
