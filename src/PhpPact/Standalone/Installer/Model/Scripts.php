<?php

namespace PhpPact\Standalone\Installer\Model;

/**
 * Represents locations of Ruby Standalone full path and scripts.
 * Class BinaryScripts.
 */
class Scripts
{
    /**
     * Path to PhpPact Mock Service.
     *
     * @var string
     */
    private $mockService;

    /**
     * Path to the PhpPact Stub Service.
     *
     * @var string
     */
    private $stubService;

    /**
     * Path to the PhpPact Pact Message.
     *
     * @var string
     */
    private $pactMessage;

    /**
     * Path to the PhpPact Provider Verifier.
     *
     * @var string
     */
    private $providerVerifier;

    /**
     * @var string
     */
    private $broker;

    public function __construct(string $mockService, string $stubService, string $providerVerifier, string $pactMessage, string $broker)
    {
        $this->mockService      = $mockService;
        $this->stubService      = $stubService;
        $this->providerVerifier = $providerVerifier;
        $this->pactMessage      = $pactMessage;
        $this->broker           = $broker;
    }

    /**
     * @return string
     */
    public function getMockService(): string
    {
        return $this->mockService;
    }

    /**
     * @return string
     */
    public function getStubService(): string
    {
        return $this->stubService;
    }

    /**
     * @return string
     */
    public function getProviderVerifier(): string
    {
        return $this->providerVerifier;
    }

    /**
     * @param string $providerVerifier
     *
     * @return Scripts
     */
    public function setProviderVerifier(string $providerVerifier): self
    {
        $this->providerVerifier = $providerVerifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getBroker(): string
    {
        return $this->broker;
    }

    /**
     * @return string
     */
    public function getPactMessage(): string
    {
        return $this->pactMessage;
    }
}
