<?php

namespace MultipartConsumer\Tests;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPactTest\Helper\ProviderProcess;
use PHPUnit\Framework\TestCase;

class PactVerifyTest extends TestCase
{
    private ProviderProcess $process;

    /**
     * Run the PHP build-in web server.
     */
    protected function setUp(): void
    {
        $this->process = new ProviderProcess(__DIR__ . '/../public/');
        $this->process->start();
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
     */
    public function testPactVerifyConsumer()
    {
        $config = new VerifierConfig();
        $config->getProviderInfo()
            ->setName('multipartProvider') // Providers name to fetch.
            ->setHost('localhost')
            ->setPort(7202);
        if ($level = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($level);
        }

        $verifier = new Verifier($config);
        $verifier->addFile(__DIR__ . '/../../pacts/multipartConsumer-multipartProvider.json');

        $verifyResult = $verifier->verify();

        $this->assertTrue($verifyResult);
    }
}
