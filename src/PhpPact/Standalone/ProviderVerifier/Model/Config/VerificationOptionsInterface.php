<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

interface VerificationOptionsInterface
{
    public function isDisableSslVerification(): bool;

    public function setDisableSslVerification(bool $disableSslVerification): self;

    public function setRequestTimeout(int $requestTimeout): self;

    public function getRequestTimeout(): int;

    /**
     * Whether the verification fails when no pacts are found (default: true).
     * Setting it to false only means an empty set of pacts doesn't fail the run;
     * pacts that are found are still verified as before.
     */
    public function isNoPactsIsError(): bool;

    public function setNoPactsIsError(bool $noPactsIsError): self;
}
