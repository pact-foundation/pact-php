<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;

/**
 * Trait VerificationOptions.
 */
trait VerificationOptions
{
    private int $requestTimeout           = 5000;
    private bool $disableSslVerification = false;

    /**
     * {@inheritdoc}
     */
    public function isDisableSslVerification(): bool
    {
        return $this->disableSslVerification;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisableSslVerification(bool $disableSslVerification): VerifierConfigInterface
    {
        $this->disableSslVerification = $disableSslVerification;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestTimeout(int $requestTimeout): VerifierConfigInterface
    {
        $this->requestTimeout = $requestTimeout;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTimeout(): int
    {
        return $this->requestTimeout;
    }
}
