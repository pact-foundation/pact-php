<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

interface VerificationOptionsInterface
{
    public function isDisableSslVerification(): bool;

    public function setDisableSslVerification(bool $disableSslVerification): self;

    public function setRequestTimeout(int $requestTimeout): self;

    public function getRequestTimeout(): int;
}
