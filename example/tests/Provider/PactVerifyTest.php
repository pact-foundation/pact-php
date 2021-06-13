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
 * This is an example on how you could use the included amphp/process wrapper to start your API to run PACT verification against a Provider.
 * Class PactVerifyTest
 */
class PactVerifyTest extends TestCase
{
    private Process $process;

    /**
     * Run the PHP build-in web server.
     */
    protected function setUp(): void
    {
        $publicPath    = __DIR__ . '/../../src/Provider/public';

        $this->process = new Process(['php', '-S', 'localhost:7202', '-t', $publicPath, $publicPath . '/proxy.php']);
        $this->process->start();
        $this->process->waitUntil(function ($type, $output) {
            return false !== \strpos($output, 'Development Server (http://localhost:7202) started');
        });
    }

    /**
     * Stop the web server process once complete.
     */
    protected function tearDown(): void
    {
        $this->process->stop();
    }

    /**
     * This test will run after the web server is started.
     *
     * @throws NoDownloaderFoundException
     * @throws FileDownloadFailureException
     */
    public function testPactVerifyConsumer()
    {
        $config = new VerifierConfig();
        $config
            ->setProviderName('someProvider') // Providers name to fetch.
            ->setProviderVersion('1.0.0') // Providers version.
            ->setProviderBranch('main') // Providers git branch
            ->setHost('localhost')
            ->setPort(7202)
            ->setStateChangeUrl(new Uri('http://localhost:7202/change-state'))
            ; // Flag the verifier service to publish the results to the Pact Broker.

        // Verify that the Consumer 'someConsumer' that is tagged with 'master' is valid.
        $verifier = new Verifier();
        $verifier->newHandle($config);
        $verifier->addDirectory(__DIR__ . '/../../pacts/');

        $this->assertTrue($verifier->verify());
    }
}
