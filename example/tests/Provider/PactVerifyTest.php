<?php

namespace Provider;

use Amp\Process\Process;
use Amp\Process\ProcessException;
use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PHPUnit\Framework\TestCase;

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

        \Amp\Loop::run(function () use ($publicPath) {
            $this->process = new Process('exec php' . ' ' . \implode(' ', ['-S', 'localhost:7202', '-t', $publicPath]));

            print "\n" . $this->process->getCommand() . "\n";
            $this->process->start();

//            $stream = $this->process->getStdout();
//            print yield $stream->read();

            if (!$this->process->isRunning()) {
                throw new ProcessException('Failed to start mock server');
            }

            \Amp\Loop::delay($msDelay = 1000, 'Amp\\Loop::stop');
        });
    }

    /**
     * Stop the web server process once complete.
     * @throws ProcessException
     */
    protected function tearDown()
    {
        $this->process->signal(15);
        $this->process->getPid()->onResolve(function ($error, $pid) {
            if ($error) {
                throw new ProcessException($error);
            }

            print "\nStopping Process Id: {$pid}\n";
            $this->process->signal(15);
            proc_open("kill -9 $pid", array(2 => array('pipe', 'w')), $pipes);
        });
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
