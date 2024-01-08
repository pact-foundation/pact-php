<?php

namespace ProtobufSyncMessageProvder\Tests;

use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransport;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PactVerifyTest extends TestCase
{
    private Process $process;

    protected function setUp(): void
    {
        $this->process = new Process([__DIR__ . '/../bin/roadrunner/rr', 'serve', '-w', __DIR__ . '/..']);
        $this->process->setTimeout(120);

        $this->process->start(function (string $type, string $buffer): void {
            echo "\n$type > $buffer";
        });
        $this->process->waitUntil(fn () => is_resource(@fsockopen('127.0.0.1', 9001)));
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
            ->setPort(9001)
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
