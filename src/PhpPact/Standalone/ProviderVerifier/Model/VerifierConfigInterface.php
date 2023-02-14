<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Psr\Http\Message\UriInterface;

/**
 * Configuration to use with the verifier server.
 * Interface VerifierServerConfigInterface.
 */
interface VerifierConfigInterface
{
    /**
     * @return null|UriInterface providers base url
     */
    public function getProviderBaseUrl(): ?UriInterface;

    /**
     * @param UriInterface $providerBaseUrl providers base url
     */
    public function setProviderBaseUrl(UriInterface $providerBaseUrl): self;

    /**
     * @return null|string Base URL to setup the provider states at
     */
    public function getProviderStatesSetupUrl(): ?string;

    /**
     * @param string $providerStatesSetupUrl Base URL to setup the provider states at
     */
    public function setProviderStatesSetupUrl(string $providerStatesSetupUrl): self;

    /**
     * @return null|string name of the provider
     */
    public function getProviderName(): ?string;

    /**
     * @param string $providerName Name of the provider
     */
    public function setProviderName(string $providerName): self;

    /**
     * @return null|string providers version
     */
    public function getProviderVersion(): ?string;

    /**
     * @param string $providerVersion providers version
     */
    public function setProviderVersion(string $providerVersion): self;

    /**
     * @param string $providerBranch providers branch name
     */
    public function setProviderBranch(string $providerBranch): self;

    /**
     * @return array<int, string> providers version tag
     */
    public function getProviderVersionTag(): array;

    /**
     * @return null|string providers branch name
     */
    public function getProviderBranch(): ?string;

    /**
     * @param string $providerVersionTag providers version tag
     */
    public function setProviderVersionTag(string $providerVersionTag): self;

    /**
     * @return array<int, string> consumers version tag
     */
    public function getConsumerVersionTag(): array;

    /**
     * @param string $consumerVersionTag consumers version tag
     */
    public function addConsumerVersionTag(string $consumerVersionTag): self;

    /**
     * @param string $providerVersionTag provider version tag
     */
    public function addProviderVersionTag(string $providerVersionTag): self;

    public function getConsumerVersionSelectors(): ConsumerVersionSelectors;

    /**
     * @param ConsumerVersionSelectors $selectors Consumer version selectors
     */
    public function setConsumerVersionSelectors(ConsumerVersionSelectors $selectors): self;

    /**
     * @return bool are results going to be published
     */
    public function isPublishResults(): bool;

    /**
     * @param bool $publishResults flag to publish results
     */
    public function setPublishResults(bool $publishResults): self;

    /**
     * @return null|UriInterface url to the pact broker
     */
    public function getBrokerUri(): ?UriInterface;

    /**
     * @param UriInterface $brokerUri uri to the pact broker
     */
    public function setBrokerUri(UriInterface $brokerUri): self;

    /**
     * @return null|string token for the pact broker
     */
    public function getBrokerToken(): ?string;

    /**
     * @param null|string $brokerToken token for the pact broker
     */
    public function setBrokerToken(?string $brokerToken): self;

    /**
     * @return null|string username for the pact broker if secured
     */
    public function getBrokerUsername(): ?string;

    /**
     * @param string $brokerUsername username for the pact broker if secured
     */
    public function setBrokerUsername(string $brokerUsername): self;

    /**
     * @return null|string password for the pact broker if secured
     */
    public function getBrokerPassword(): ?string;

    /**
     * @param string $brokerPassword password for the pact broker if secured
     */
    public function setBrokerPassword(string $brokerPassword): self;

    /**
     * @return array<int, string> custom headers for the request to the provider such as authorization
     */
    public function getCustomProviderHeaders(): array;

    /**
     * @param array<int, string> $customProviderHeaders custom headers for the requests to the provider such as authorization
     */
    public function setCustomProviderHeaders(array $customProviderHeaders): self;

    public function addCustomProviderHeader(string $name, string $value): self;

    /**
     * @return bool is verbosity level increased
     */
    public function isVerbose(): bool;

    /**
     * @param bool $verbose increase verbosity level
     */
    public function setVerbose(bool $verbose): self;

    /**
     * @return null|string set the directory for the pact.log file
     */
    public function getLogDirectory(): ?string;

    /**
     * @param string $log set the directory for the pact.log file
     */
    public function setLogDirectory(string $log): self;

    /**
     * @return null|string RSpec formatter. Defaults to custom Pact formatter. json and RspecJunitFormatter may also be used
     */
    public function getFormat(): ?string;

    /**
     * @param string $format RSpec formatter. Defaults to custom Pact formatter. json and RspecJunitFormatter may also be used
     */
    public function setFormat(string $format): self;

    public function setProcessTimeout(int $timeout): self;

    public function setProcessIdleTimeout(int $timeout): self;

    public function getProcessTimeout(): int;

    public function getProcessIdleTimeout(): int;

    /**
     * @param bool $pending allow pacts which are in pending state to be verified without causing the overall task to fail
     */
    public function setEnablePending(bool $pending): self;

    /**
     * @return bool is enabled pending pacts
     */
    public function isEnablePending(): bool;

    /**
     * @param string $date Includes pact marked as WIP since this date.
     *                     Accepted formats: Y-m-d (2020-01-30) or c (ISO 8601 date 2004-02-12T15:19:21+00:00)
     */
    public function setIncludeWipPactSince(string $date): self;

    /**
     * @return null|string get start date of included WIP Pacts
     */
    public function getIncludeWipPactSince();

    /**
     * @return null|callable
     */
    public function getRequestFilter(): ?callable;

    /**
     * @param callable $requestFilter
     */
    public function setRequestFilter(callable $requestFilter): self;
}
