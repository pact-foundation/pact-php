<?php

namespace MessageProvider\Tests;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransport;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPactTest\Helper\PhpProcess;
use PHPUnit\Framework\TestCase;

class PactVerifyTest extends TestCase
{
    private PhpProcess $process;

    protected function setUp(): void
    {
        $this->process = new PhpProcess(__DIR__ . '/../public/');
        $this->process->start();
    }

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
            ->setName('messageProvider') // Providers name to fetch.
            ->setHost('localhost')
            ->setPort($this->process->getPort());
        $config->getProviderState()
            ->setStateChangeUrl(new Uri(sprintf('http://localhost:%d/pact-change-state', $this->process->getPort())))
        ;
        $config->addProviderTransport(
            (new ProviderTransport())
                ->setProtocol(ProviderTransport::MESSAGE_PROTOCOL)
                ->setPort($this->process->getPort())
                ->setPath('/pact-messages')
                ->setScheme('http')
        );
        if ($level = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($level);
        }

        $verifier = new Verifier($config);
        $verifier->addFile(__DIR__ . '/../../pacts/messageConsumer-messageProvider.json');

        $verifyResult = $verifier->verify();

        $this->assertTrue($verifyResult);
    }
}
