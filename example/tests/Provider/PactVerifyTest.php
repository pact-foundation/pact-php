<?php

namespace Provider;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
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
        $publicPath    =  __DIR__ . '/../../src/Provider/public/';
        $this->process = new Process("php -S localhost:7202 -t {$publicPath}");
        $this->process->start();
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
            ->setProviderBaseUrl(new Uri('http://localhost:7202')) // URL of the Provider.
            ; // Flag the verifier service to publish the results to the Pact Broker.

        // Verify that the Consumer 'someConsumer' that is tagged with 'master' is valid.
        $verifier = new Verifier($config);
        $verifier->verifyFiles([__DIR__ . '/../../output/someconsumer-someprovider.json']);

        // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
        $this->assertTrue(true, 'Pact Verification has failed.');
    }
}
