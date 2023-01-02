<?php

namespace Provider;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransport;
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

        $this->processRunner = new ProcessRunner('php', ['-S', 'localhost:7202', '-t', $publicPath]);

        $this->processRunner->run();
        \usleep(300000); // wait for server to start
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
        $config->getProviderInfo()
            ->setName('someProvider') // Providers name to fetch.
            ->setHost('localhost')
            ->setPort(7202);
        $config->getProviderState()
            ->setStateChangeUrl(new Uri('http://localhost:7202/pact-change-state'))
        ;
        $config->addProviderTransport(
            (new ProviderTransport())
                ->setProtocol(ProviderTransport::MESSAGE_PROTOCOL)
                ->setPort(7202)
                ->setPath('/pact-messages')
                ->setScheme('http')
        );
        if ($level = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($level);
        }

        // Verify that the Consumer 'someConsumer' that is tagged with 'master' is valid.
        $verifier = new Verifier($config);
        $verifier->addFile(__DIR__ . '/../../pacts/someconsumer-someprovider.json');
        $verifier->addFile(__DIR__ . '/../../pacts/test_consumer-test_provider.json');

        $verifyResult = $verifier->verify();

        $this->assertTrue($verifyResult);
    }
}
