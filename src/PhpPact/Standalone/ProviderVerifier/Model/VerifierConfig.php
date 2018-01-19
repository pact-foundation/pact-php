<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

class VerifierConfig implements VerifierConfigInterface
{
    /** @var string */
    private $providerBaseUrl;

    /** @var null|string */
    private $providerStatesSetupUrl;

    /** @var string */
    private $providerName;

    /** @var string */
    private $providerVersion;

    /** @var bool */
    private $publishResults = false;

    /** @var null|string */
    private $brokerUsername;

    /** @var null|string */
    private $brokerPassword;

    /** @var string[] */
    private $customProviderHeaders;

    /** @var bool */
    private $verbose = false;

    /** @var string */
    private $format;

    /** @var string[] */
    private $pactUrls;

    /**
     * @return string
     */
    public function getProviderBaseUrl(): string
    {
        return $this->providerBaseUrl;
    }

    /**
     * @param string $providerBaseUrl
     *
     * @return VerifierConfigInterface
     */
    public function setProviderBaseUrl(string $providerBaseUrl): VerifierConfigInterface
    {
        $this->providerBaseUrl = $providerBaseUrl;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProviderStatesSetupUrl()
    {
        return $this->providerStatesSetupUrl;
    }

    /**
     * @param string $providerStatesSetupUrl
     *
     * @return VerifierConfigInterface
     */
    public function setProviderStatesSetupUrl(string $providerStatesSetupUrl): VerifierConfigInterface
    {
        $this->providerStatesSetupUrl = $providerStatesSetupUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * @param string $providerName
     *
     * @return VerifierConfigInterface
     */
    public function setProviderName(string $providerName): VerifierConfigInterface
    {
        $this->providerName = $providerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderVersion(): string
    {
        return $this->providerVersion;
    }

    /**
     * @param string $providerVersion
     *
     * @return VerifierConfigInterface
     */
    public function setProviderVersion(string $providerVersion): VerifierConfigInterface
    {
        $this->providerVersion = $providerVersion;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublishResults(): bool
    {
        return $this->publishResults;
    }

    /**
     * @param bool $publishResults
     *
     * @return VerifierConfigInterface
     */
    public function setPublishResults(bool $publishResults): VerifierConfigInterface
    {
        $this->publishResults = $publishResults;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBrokerUsername()
    {
        return $this->brokerUsername;
    }

    /**
     * @param string $brokerUsername
     *
     * @return VerifierConfigInterface
     */
    public function setBrokerUsername(string $brokerUsername)
    {
        $this->brokerUsername = $brokerUsername;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBrokerPassword()
    {
        return $this->brokerPassword;
    }

    /**
     * @param string $brokerPassword
     *
     * @return VerifierConfigInterface
     */
    public function setBrokerPassword(string $brokerPassword)
    {
        $this->brokerPassword = $brokerPassword;

        return $this;
    }

    /**
     * @return null|\string[]
     */
    public function getCustomProviderHeaders()
    {
        return $this->customProviderHeaders;
    }

    /**
     * @param \string[] $customProviderHeaders
     *
     * @return VerifierConfigInterface
     */
    public function setCustomProviderHeaders(array $customProviderHeaders): VerifierConfigInterface
    {
        $this->customProviderHeaders = $customProviderHeaders;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return VerifierConfigInterface
     */
    public function addCustomProviderHeader(string $name, string $value): VerifierConfigInterface
    {
        $this->customProviderHeaders[] = "{$name}: {$value}";

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * @param bool $verbose
     *
     * @return VerifierConfigInterface
     */
    public function setVerbose(bool $verbose): VerifierConfigInterface
    {
        $this->verbose = $verbose;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return VerifierConfigInterface
     */
    public function setFormat(string $format): VerifierConfigInterface
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return \string[]
     */
    public function getPactUrls(): array
    {
        return $this->pactUrls;
    }

    /**
     * @param string[] $pactUrls
     *
     * @return VerifierConfigInterface
     */
    public function setPactUrls(array $pactUrls): VerifierConfigInterface
    {
        $this->pactUrls = $pactUrls;

        return $this;
    }
}
