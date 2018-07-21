<?php

namespace MessageProvider;

use PhpPact\Provider\MessageVerifier;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PHPUnit\Framework\TestCase;

/**
 * Class MessageProviderTest
 */
class MessageProviderTest extends TestCase
{
    public static function setUpBeforeClass()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::tearDownAfterClass();
    }

    /**
     * @throws \Exception
     */
    public function testProcess()
    {
        $provider        = new MessageProvider();
        $callback        = [$provider, 'PublishAnotherMessageType'];

        $config = new VerifierConfig();
        $config
            ->setProviderName('SomeProvider') // Providers name to fetch.
            ->setPublishResults(false); // Flag the verifier service to publish the results to the Pact Broker.

        // Verify that the Consumer 'SomeConsumer' that is tagged with 'master' is valid.
        $verifier = (new MessageVerifier($config))
                        ->setCallback($callback)
                        ->verifyFiles(['D:\\Temp\\test_consumer-test_provider-rev1.json']);

        // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
        $this->assertTrue(true, 'Pact Verification has failed.');

        $this->assertFalse(false, 'Expects verification to pass without exceptions being thrown');
    }
}