<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Psr\Http\Message\UriInterface;

class VerifierConfig implements VerifierConfigInterface
{
    /** @var UriInterface */
    private $providerBaseUrl;

    /** @var null|string */
    private $providerStatesSetupUrl;

    /** @var string */
    private $providerName;

    /** @var string */
    private $providerVersion;

    /** @var bool */
    private $publishResults = false;

    /** @var UriInterface */
    private $brokerUri;

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

    /**
     * @return UriInterface
     */
    public function getProviderBaseUrl(): UriInterface
    {
        return $this->providerBaseUrl;
    }

    /**
     * @param UriInterface $providerBaseUrl
     *
     * @return VerifierConfigInterface
     */
    public function setProviderBaseUrl(UriInterface $providerBaseUrl): VerifierConfigInterface
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
     * @return UriInterface
     */
    public function getBrokerUri(): UriInterface
    {
        return $this->brokerUri;
    }

    /**
     * @param UriInterface $brokerUri
     *
     * @return VerifierConfig
     */
    public function setBrokerUri(UriInterface $brokerUri): self
    {
        $this->brokerUri = $brokerUri;

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
}
