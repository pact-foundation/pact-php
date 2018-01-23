<?php

namespace Provider;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Broker\Service\BrokerHttpService;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * This is an example on how you could use the Symfony Process library to start your API to run PACT verification against a Provider.
 * Class PactVerifyTest
 */
class PactVerifyTest extends TestCase
{
    /** @var Process */
    private $process;

    /**
     * Run the PHP build-in web server.
     */
    protected function setUp()
    {
        $this->process = new Process('php -S localhost:58000 -t ../../src/Provider/public/');
        $this->process->start();
        \sleep(1);
    }

    /**
     * Stop the web server process once complete.
     */
    protected function tearDown()
    {
        $this->process->stop();
    }

    /**
     * This test will run after the web server is started.
     */
    public function testPactVerifyConsumer()
    {
        $config = new VerifierConfig();
        $config
            ->setProviderName('SomeProvider')
            ->setProviderVersion('1.0.0')
            ->setProviderBaseUrl(new Uri('http://localhost:58000'))
            ->setBrokerUri(new Uri('http://localhost'))
            ->setPublishResults(true);

        // Download the Pact File.
        $verifier = new Verifier($config, new BrokerHttpService(new GuzzleClient(), $config->getBrokerUri()), new InstallManager());
        $verifier->verify('SomeConsumer', 'standalone-provider');

        // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
        $this->assertTrue(true, 'Pact Verification has failed.');
    }
}
