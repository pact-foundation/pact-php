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
    /**
     * @throws \Exception
     */
    public function testProcess()
    {
        $callbacks                                         = [];
        $callbacks['an alligator named Mary exists']       = function () {
            $content       = new \stdClass();
            $content->text ='Hello Mary';

            $metadata          = [];
            $metadata['queue'] = 'myKey';

            $provider = (new ExampleMessageProvider())
                ->setContents($content)
                ->setMetadata($metadata);

            return $provider->Build();
        };

        $callbacks['footprints dressed in red'] = function () {
            $content       = new \stdClass();
            $content->song ='And the wind whispers Mary';

            $metadata          = [];
            $metadata['queue'] = 'myKey';

            $provider = (new ExampleMessageProvider())
                ->setContents($content)
                ->setMetadata($metadata);

            return $provider->Build();
        };

        $config = new VerifierConfig();
        $config
            ->setProviderName('someProvider') // Providers name to fetch.
            ->setPublishResults(false); // Flag the verifier service to publish the results to the Pact Broker.

        // Verify that the Consumer 'someConsumer' that is tagged with 'master' is valid.
        $verifier = (new MessageVerifier($config))
            ->setCallbacks($callbacks)
            ->verifyFiles([__DIR__ . '/../../pacts/test_consumer-test_provider.json']);

        // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
        $this->assertTrue(true, 'Expects to reach true by running verification');
    }
}
