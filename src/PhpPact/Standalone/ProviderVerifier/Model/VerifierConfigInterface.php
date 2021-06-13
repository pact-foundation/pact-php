<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Psr\Http\Message\UriInterface;

/**
 * Interface VerifierServerConfigInterface.
 */
interface VerifierConfigInterface
{
    /**
     * @return null|string
     */
    public function getBasePath(): ?string;

    /**
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath(string $basePath): self;

    /**
     * @return null|UriInterface
     */
    public function getStateChangeUrl(): ?UriInterface;

    /**
     * @param UriInterface $stateChangeUrl
     *
     * @return $this
     */
    public function setStateChangeUrl(UriInterface $stateChangeUrl): self;

    /**
     * @return null|string
     */
    public function getProviderName(): ?string;

    /**
     * @param string $providerName
     *
     * @return $this
     */
    public function setProviderName(string $providerName): self;

    /**
     * @return string
     */
    public function getProviderVersion(): string;

    /**
     * @param string $providerVersion
     *
     * @return $this
     */
    public function setProviderVersion(string $providerVersion): self;

    /**
     * @return array
     */
    public function getProviderTags(): array;

    /**
     * @param array|string[] $providerTags
     *
     * @return $this
     */
    public function setProviderTags(array $providerTags): self;

    /**
     * @return string|null
     */
    public function getProviderBranch(): ?string;

    /**
     * @param string $providerBranch
     *
     * @return $this
     */
    public function setProviderBranch(string $providerBranch): VerifierConfigInterface;

    /**
     * @return bool
     */
    public function isPublishResults(): bool;

    /**
     * @param bool $publishResults
     *
     * @return $this
     */
    public function setPublishResults(bool $publishResults): self;

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
     * @param bool $stateChangeAsQuery
     *
     * @return $this
     */
    public function setStateChangeAsQuery(bool $stateChangeAsQuery): self;

    /**
     * @return bool
     */
    public function isStateChangeAsQuery(): bool;

    /**
     * @param bool $stateChangeTeardown
     *
     * @return $this
     */
    public function setStateChangeTeardown(bool $stateChangeTeardown): self;

    /**
     * @return bool
     */
    public function isStateChangeTeardown(): bool;

    /**
     * @return null|UriInterface
     */
    public function getBuildUrl(): ?UriInterface;

    /**
     * @param UriInterface $buildUrl
     *
     * @return $this
     */
    public function setBuildUrl(UriInterface $buildUrl): self;

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

    /**
     * @param string ...$filterConsumerNames
     *
     * @return $this
     */
    public function setFilterConsumerNames(string ...$filterConsumerNames): self;

    /**
     * @return array
     */
    public function getFilterConsumerNames(): array;

    /**
     * @param string $filterDescription
     *
     * @return $this
     */
    public function setFilterDescription(string $filterDescription): self;

    /**
     * @return null|string
     */
    public function getFilterDescription(): ?string;

    /**
     * @param bool $filterNoState
     *
     * @return $this
     */
    public function setFilterNoState(bool $filterNoState): self;

    /**
     * @return bool
     */
    public function getFilterNoState(): bool;

    /**
     * @param string $filterState
     *
     * @return $this
     */
    public function setFilterState(string $filterState): self;

    /**
     * @return null|string
     */
    public function getFilterState(): ?string;

    /**
     * @return null|string
     */
    public function getScheme(): ?string;

    /**
     * @param string $scheme
     *
     * @return $this
     */
    public function setScheme(string $scheme): self;

    /**
     * @return null|string
     */
    public function getHost(): ?string;

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost(string $host): self;

    /**
     * @return null|int
     */
    public function getPort(): ?int;

    /**
     * @param int $port
     *
     * @return $this
     */
    public function setPort(int $port): self;
}
