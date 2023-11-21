<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

interface VerifierLoggerInterface
{
    public function log(string $output): void;
}
