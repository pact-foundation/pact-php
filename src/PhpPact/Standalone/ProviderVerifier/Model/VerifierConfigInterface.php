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
     * @return null|UriInterface providers base url
     */
    public function getProviderBaseUrl();

    /**
     * @param UriInterface $providerBaseUrl providers base url
     *
     * @return VerifierConfigInterface
     */
    public function setProviderBaseUrl(UriInterface $providerBaseUrl): self;

    /**
     * @return null|string Base URL to setup the provider states at
     */
    public function getProviderStatesSetupUrl();

    /**
     * @param string $providerStatesSetupUrl Base URL to setup the provider states at
     *
     * @return VerifierConfigInterface
     */
    public function setProviderStatesSetupUrl(string $providerStatesSetupUrl): self;

    /**
     * @return null|string name of the provider
     */
    public function getProviderName();

    /**
     * @param string $name Name of the provider
     *
     * @return VerifierConfigInterface
     */
    public function setProviderName(string $name): self;

    /**
     * @return null|string providers version
     */
    public function getProviderVersion();

    /**
     * @param string $providerAppVersion providers version
     *
     * @return VerifierConfigInterface
     */
    public function setProviderVersion(string $providerAppVersion): self;

    /**
     * @return bool are results going to be published
     */
    public function isPublishResults(): bool;

    /**
     * @param bool $publishResults flag to publish results
     *
     * @return VerifierConfigInterface
     */
    public function setPublishResults(bool $publishResults): self;

    /**
     * @return null|UriInterface url to the pact broker
     */
    public function getBrokerUri();

    /**
     * @param UriInterface $brokerUri uri to the pact broker
     *
     * @return VerifierConfigInterface
     */
    public function setBrokerUri(UriInterface $brokerUri): self;

    /**
     * @return null|string username for the pact broker if secured
     */
    public function getBrokerUsername();

    /**
     * @param string $brokerUsername username for the pact broker if secured
     *
     * @return VerifierConfigInterface
     */
    public function setBrokerUsername(string $brokerUsername);

    /**
     * @return null|string password for the pact broker if secured
     */
    public function getBrokerPassword();

    /**
     * @param string $brokerPassword password for the pact broker if secured
     *
     * @return VerifierConfigInterface
     */
    public function setBrokerPassword(string $brokerPassword);

    /**
     * @return null|\string[] custom headers for the request to the provider such as authorization
     */
    public function getCustomProviderHeaders();

    /**
     * @param \string[] $customProviderHeaders custom headers for the requests to the provider such as authorization
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
     * @return bool is verbosity level increased
     */
    public function isVerbose(): bool;

    /**
     * @param bool $verbose increase verbosity level
     *
     * @return VerifierConfigInterface
     */
    public function setVerbose(bool $verbose): self;

    /**
     * @return null|string RSpec formatter. Defaults to custom Pact formatter. json and RspecJunitFormatter may also be used
     */
    public function getFormat();

    /**
     * @param string $format RSpec formatter. Defaults to custom Pact formatter. json and RspecJunitFormatter may also be used
     *
     * @return VerifierConfigInterface
     */
    public function setFormat(string $format): self;

    /**
     * @param int $timeout
     * @return VerifierConfigInterface
     */
    public function setProcessTimeout(int $timeout): self;

    /**
     * @param int $timeout
     * @return VerifierConfigInterface
     */
    public function setProcessIdleTimeout(int $timeout): self;

    /**
     * @return int
     */
    public function getProcessTimeout(): int;

    /**
     * @return int
     */
    public function getProcessIdleTimeout(): int;
}
