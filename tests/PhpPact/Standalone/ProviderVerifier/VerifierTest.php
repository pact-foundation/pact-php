<?php

namespace PhpPactTest\Standalone\ProviderVerifier;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPactTest\Helper\ProviderProcess;
use PHPUnit\Framework\TestCase;

class VerifierTest extends TestCase
{
    private ProviderProcess $process;

    /**
     * Run the PHP build-in web server.
     */
    protected function setUp(): void
    {
        $this->process = new ProviderProcess(__DIR__ . '/../../../_public/');
        $this->process->start();
    }

    /**
     * Stop the web server process once complete.
     */
    protected function tearDown(): void
    {
        $this->process->stop();
    }

    public function testVerify(): void
    {
        $config = new VerifierConfig();
        $config->getProviderInfo()
            ->setName('someProvider')
            ->setHost('localhost')
            ->setPort(7202)
            ->setScheme('http')
            ->setPath('/');
        if ($level = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($level);
        }

        $verifier = new Verifier($config);
        $verifier->addFile(__DIR__ . '/../../../_resources/someconsumer-someprovider.json');

        $verifyResult = $verifier->verify();

        $this->assertTrue($verifyResult);
    }
}
