<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Standalone\ProviderVerifier\Model\Source\Broker;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPactTest\CompatibilitySuite\Model\Logger;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Model\VerifyResult;

final class ProviderVerifier implements ProviderVerifierInterface
{
    private array $sources = [];
    private VerifierConfigInterface $config;

    private VerifyResult $verifyResult;

    public function __construct()
    {
        $this->config = new VerifierConfig();
        $this->config
            ->getProviderInfo()
                ->setName(PactPath::PROVIDER)
                ->setHost('localhost');
    }

    public function getConfig(): VerifierConfigInterface
    {
        return $this->config;
    }

    public function verify(): void
    {
        $logger = new Logger();
        $verifier = new Verifier($this->config, $logger);
        foreach ($this->sources as $source) {
            if ($source instanceof Broker) {
                $verifier->addBroker($source);
            } else {
                $verifier->addFile($source);
            }
        }

        $success = $verifier->verify();
        $this->verifyResult = new VerifyResult($success, $logger->getOutput());
    }

    public function addSource(string|Broker $source): void
    {
        if (in_array($source, $this->sources)) {
            return;
        }
        if ($source instanceof Broker) {
            $this->sources = array_filter($this->sources, fn (mixed $source) => !$source instanceof Broker);
        }
        $this->sources[] = $source;
    }

    public function getVerifyResult(): VerifyResult
    {
        return $this->verifyResult;
    }
}
