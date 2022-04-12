<?php

namespace Provider;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPact\Standalone\Runner\ProcessRunner;
use PHPUnit\Framework\TestCase;

/**
 * This is an example on how you could use the included amphp/process wrapper to start your API to run PACT verification against a Provider.
 * Class PactVerifyTest
 */
class PactVerifyTest extends TestCase
{
    /** @var ProcessRunner */
    private $processRunner;

    /**
     * Run the PHP build-in web server.
     */
    protected function setUp(): void
    {
        $publicPath    =  __DIR__ . '/../../src/Provider/public/';

        $this->processRunner = new ProcessRunner('php', ['-S', 'localhost:7202', '-t', $publicPath]);

        $this->processRunner->run();
    }

    /**
     * Stop the web server process once complete.
     */
    protected function tearDown(): void
    {
        $this->processRunner->stop();
    }

    /**
     * This test will run after the web server is started.
     *
     * @throws FileDownloadFailureException
     * @throws NoDownloaderFoundException
     */
    public function testPactVerifyConsumer()
    {
        $config = new VerifierConfig();
        $config
            ->setProviderName('someProvider') // Providers name to fetch.
            ->setProviderVersion('1.0.0') // Providers version.
            ->setProviderBranch('main') // Providers git branch
            ->setProviderBaseUrl(new Uri('http://localhost:7202')) // URL of the Provider.
            ; // Flag the verifier service to publish the results to the Pact Broker.

        // Verify that the Consumer 'someConsumer' that is tagged with 'master' is valid.
        $verifier = new Verifier($config);
        $verifier->verifyFiles([__DIR__ . '/../../pacts/someconsumer-someprovider.json']);

        // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
        $this->assertTrue(true, 'Pact Verification has failed.');
    }
}
