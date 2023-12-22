<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Standalone\ProviderVerifier\Model\Source\Broker;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use PhpPactTest\CompatibilitySuite\Model\VerifyResult;

interface ProviderVerifierInterface
{
    public function getConfig(): VerifierConfigInterface;

    public function verify(): void;

    public function addSource(string|Broker $source): void;

    public function getVerifyResult(): VerifyResult;
}
