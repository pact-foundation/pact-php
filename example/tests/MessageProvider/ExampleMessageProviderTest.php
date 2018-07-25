<?php

namespace MessageProvider;

use PhpPact\Provider\MessageVerifier;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PHPUnit\Framework\TestCase;

/**
 * Class ExampleMessageProviderTest
 */
class ExampleMessageProviderTest extends TestCase
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

        $content = new \stdClass();
        $content->song ="And the wind whispers Mary";

        $metadata = array();
        $metadata['queue'] = "myKey";

        $provider = (new ExampleMessageProvider())
                    ->setContents($content)
                    ->setMetadata($metadata);

        $callback        = [$provider, 'Build'];

        $config = new VerifierConfig();
        $config
            ->setProviderName('someProvider') // Providers name to fetch.
            ->setPublishResults(false); // Flag the verifier service to publish the results to the Pact Broker.

        // Verify that the Consumer 'someConsumer' that is tagged with 'master' is valid.
        $hasException = false;

        try {
            $verifier = (new MessageVerifier($config))
                ->setCallback($callback)
                ->verifyFiles([__DIR__ . '/../../output/test_consumer-test_provider.json']);
        } catch (\Exception $e) {
            $hasException = true;
        }

        // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
        $this->assertFalse($hasException, 'Expects verification to pass without exceptions being thrown');
    }
}
