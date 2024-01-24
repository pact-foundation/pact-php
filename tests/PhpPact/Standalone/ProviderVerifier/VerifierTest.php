<?php

namespace PhpPactTest\Standalone\ProviderVerifier;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPactTest\Helper\PhpProcess;
use PHPUnit\Framework\TestCase;

class VerifierTest extends TestCase
{
    private PhpProcess $process;

    protected function setUp(): void
    {
        $this->process = new PhpProcess(__DIR__ . '/../../../_public/');
        $this->process->start();
    }

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
            ->setPort($this->process->getPort())
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
