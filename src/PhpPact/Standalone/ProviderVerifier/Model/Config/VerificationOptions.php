<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

class VerificationOptions implements VerificationOptionsInterface
{
    private int $requestTimeout           = 5000;
    private bool $disableSslVerification = false;

    public function isDisableSslVerification(): bool
    {
        return $this->disableSslVerification;
    }

    public function setDisableSslVerification(bool $disableSslVerification): self
    {
        $this->disableSslVerification = $disableSslVerification;

        return $this;
    }

    public function setRequestTimeout(int $requestTimeout): self
    {
        $this->requestTimeout = $requestTimeout;

        return $this;
    }

    public function getRequestTimeout(): int
    {
        return $this->requestTimeout;
    }
}
