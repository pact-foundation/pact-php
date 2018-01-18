<?php

namespace Provider;

use GuzzleHttp\Client;
use PhpPact\PactVerifier;
use PhpPact\PactVerifierConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PactVerifyTest extends TestCase
{
    /** @var Process */
    private $process;

    protected function setUp()
    {
        $this->process = new Process('php -S localhost:8080 -t ../../src/Provider/public/');
        $this->process->start();
    }

    protected function tearDown()
    {
        $this->process->stop();
    }

    public function testPactVerify()
    {
        $httpClient = new Client();

        $config = new PactVerifierConfig();
        $config->setBaseUri('http://localhost:8080');
        $pactVerifier = new PactVerifier($config);

        $pactVerifier
            ->providerState('A GET request to get types')
            ->serviceProvider('MockApiProvider', $httpClient)
            ->honoursPactWith('MockApiConsumer')
            ->pactUri('../pact/mockapiconsumer-mockapiprovider.json')
            ->verify();
    }
}
