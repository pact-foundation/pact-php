<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

/**
 * Interface VerificationOptionsInterface.
 */
interface VerificationOptionsInterface
{
    /**
     * @return bool
     */
    public function isDisableSslVerification(): bool;

    /**
     * @param bool $disableSslVerification
     *
     * @return $this
     */
    public function setDisableSslVerification(bool $disableSslVerification): self;

    /**
     * @param int $requestTimeout
     *
     * @return $this
     */
    public function setRequestTimeout(int $requestTimeout): self;

    /**
     * @return int
     */
    public function getRequestTimeout(): int;
}
