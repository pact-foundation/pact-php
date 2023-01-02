<?php

namespace Provider;

use GuzzleHttp\Psr7\Uri;
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
    private ProcessRunner $processRunner;

    /**
     * Run the PHP build-in web server.
     */
    protected function setUp(): void
    {
        $publicPath    =  __DIR__ . '/../../src/Provider/public/';

        $this->processRunner = new ProcessRunner('php', ['-S', 'localhost:7202', '-t', $publicPath, $publicPath . 'proxy.php']);

        $this->processRunner->run();
        \sleep(1); // wait for server to start
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
        $verifier = new Verifier($config);
        $verifier->addFile(__DIR__ . '/../../pacts/someconsumer-someprovider.json');
        $verifier->addFile(__DIR__ . '/../../pacts/test_consumer-test_provider.json');

        $this->assertTrue($verifier->verify());
    }
}
