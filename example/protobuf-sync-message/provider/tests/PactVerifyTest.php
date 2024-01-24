<?php

namespace ProtobufSyncMessageProvider\Tests;

use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransport;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PHPUnit\Framework\TestCase;

class PactVerifyTest extends TestCase
{
    private RoadRunnerProcess $process;

    protected function setUp(): void
    {
        $this->process = new RoadRunnerProcess();
        $this->process->start();
    }

    protected function tearDown(): void
    {
        $this->process->stop();
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
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }

        $verifier = new Verifier($config);
        $verifier->addFile(__DIR__ . '/../../pacts/protobufSyncMessageConsumer-protobufSyncMessageProvider.json');

        $this->assertTrue($verifier->verify());
    }
}
