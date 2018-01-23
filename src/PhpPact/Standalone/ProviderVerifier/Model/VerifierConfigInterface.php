<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Psr\Http\Message\UriInterface;

/**
 * Configuration to use with the verifier server.
 * Interface VerifierServerConfigInterface
 */
interface VerifierConfigInterface
{
    /**
     * @return UriInterface Providers base url
     */
    public function getProviderBaseUrl(): UriInterface;

    /**
     * @param UriInterface $providerBaseUrl providers base url
     *
     * @return VerifierConfigInterface
     */
    public function setProviderBaseUrl(UriInterface $providerBaseUrl): self;

    /**
     * @return null|string
     */
    public function getProviderStatesSetupUrl();

    /**
     * @param string $providerStatesSetupUrl
     *
     * @return VerifierConfigInterface
     */
    public function setProviderStatesSetupUrl(string $providerStatesSetupUrl): self;

    /**
     * @return string name of the provider
     */
    public function getProviderName(): string;

    /**
     * @param string $name Name of the provider
     *
     * @return VerifierConfigInterface
     */
    public function setProviderName(string $name): self;

    /**
     * @return string
     */
    public function getProviderVersion(): string;

    /**
     * @param string $providerAppVersion
     *
     * @return VerifierConfigInterface
     */
    public function setProviderVersion(string $providerAppVersion): self;

    /**
     * @return bool
     */
    public function isPublishResults(): bool;

    /**
     * @param bool $publishResults
     *
     * @return VerifierConfigInterface
     */
    public function setPublishResults(bool $publishResults): self;

    /**
     * @return UriInterface
     */
    public function getBrokerUri(): UriInterface;

    /**
     * @param UriInterface $brokerUri
     *
     * @return VerifierConfig
     */
    public function setBrokerUri(UriInterface $brokerUri): VerifierConfig;

    /**
     * @return null|string
     */
    public function getBrokerUsername();

    /**
     * @param string $brokerUsername
     *
     * @return VerifierConfigInterface
     */
    public function setBrokerUsername(string $brokerUsername);

    /**
     * @return null|string
     */
    public function getBrokerPassword();

    /**
     * @param string $brokerPassword
     *
     * @return VerifierConfigInterface
     */
    public function setBrokerPassword(string $brokerPassword);

    /**
     * @return null|\string[]
     */
    public function getCustomProviderHeaders();

    /**
     * @param \string[] $customProviderHeaders
     *
     * @return VerifierConfigInterface
     */
    public function setCustomProviderHeaders(array $customProviderHeaders): self;

    /**
     * @param string $name
     * @param string $value
     *
     * @return VerifierConfigInterface
     */
    public function addCustomProviderHeader(string $name, string $value): self;

    /**
     * @return bool
     */
    public function isVerbose(): bool;

    /**
     * @param bool $verbose
     *
     * @return VerifierConfigInterface
     */
    public function setVerbose(bool $verbose): self;

    /**
     * @return null|string
     */
    public function getFormat();

    /**
     * @param string $format
     *
     * @return VerifierConfigInterface
     */
    public function setFormat(string $format): self;

    /**
     * @return string[]
     */
    public function getPactUrls(): array;
}
