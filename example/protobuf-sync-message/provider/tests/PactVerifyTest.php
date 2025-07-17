<?php

namespace ProtobufSyncMessageProvider\Tests;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransport;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPactTest\Helper\PhpProcess;
use PHPUnit\Framework\TestCase;

class PactVerifyTest extends TestCase
{
    private gRPCServerProcess $process;
    private PhpProcess $proxy;

    protected function setUp(): void
    {
        $this->process = new gRPCServerProcess();
        $this->process->start();
        $this->proxy = new PhpProcess(__DIR__ . '/../proxy/');
        $this->proxy->start();
    }

    protected function tearDown(): void
    {
        $this->process->stop();
        $this->proxy->stop();
    }

    public function testPactVerifyConsumer(): void
    {
        $config = new VerifierConfig();
        $config->getProviderInfo()
            ->setName('protobufSyncMessageProvider')
            ->setHost('127.0.0.1');
        $providerTransport = new ProviderTransport();
        $providerTransport
            ->setProtocol('grpc')
            ->setScheme('tcp')
            ->setPort($this->process->getPort())
            ->setPath('/')
        ;
        $config->addProviderTransport($providerTransport);
        $config->getProviderState()
            ->setStateChangeUrl(new Uri(sprintf('http://localhost:%d/pact-change-state', $this->proxy->getPort())))
        ;
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }

        $verifier = new Verifier($config);
        $verifier->addFile(__DIR__ . '/../../pacts/protobufSyncMessageConsumer-protobufSyncMessageProvider.json');

        $this->assertTrue($verifier->verify());
    }
}
