<?php

namespace Provider;

use GuzzleHttp\Client;
use PhpPact\PactBrokerConnector;
use PhpPact\PactUriOptions;
use PhpPact\PactVerifier;
use PhpPact\PactVerifierConfig;
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
        $this->process = new Process('php -S localhost:8080 -t ../../src/Provider/public/');
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
     */
    public function testPactVerify()
    {
        // Download the Pact File.
        $uriOptions = new PactUriOptions('http://your-pact-broker');
        $connector  = new PactBrokerConnector($uriOptions);
        $pact       = $connector->retrievePact('MockApiProvider', 'MockApiConsumer', 'latest');

        // Verify that the PACTs are successful against your API.
        $httpClient = new Client();
        $config     = new PactVerifierConfig();
        $config->setBaseUri('http://localhost:8080');
        $pactVerifier = new PactVerifier($config);

        $pactVerifier
            ->providerState('A GET request to get types')
            ->serviceProvider('MockApiProvider', $httpClient)
            ->honoursPactWith('MockApiConsumer')
            ->pactUri($pact)
            ->verify();

        // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
        $this->assertTrue(true, 'Pact Verification has failed.');
    }
}
