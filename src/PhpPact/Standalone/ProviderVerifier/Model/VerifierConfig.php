<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Psr\Http\Message\UriInterface;

/**
 * Class VerifierConfig.
 */
class VerifierConfig implements VerifierConfigInterface
{
    private ?string $basePath             = null;
    private ?string $scheme               = null;
    private ?string $host                 = null;
    private ?int $port                    = null;
    private ?UriInterface $stateChangeUrl = null;
    private ?string $providerName         = null;
    private array $providerTags           = [];
    private ?string $providerBranch       = null;
    private ?UriInterface $buildUrl       = null;
    private int $requestTimeout           = 5000;

    private string $providerVersion;

    private array $filterConsumerNames = [];
    private ?string $filterDescription = null;
    private bool $filterNoState        = false;
    private ?string $filterState       = null;

    private bool $publishResults         = false;
    private bool $disableSslVerification = false;
    private bool $stateChangeAsQuery     = false;
    private bool $stateChangeTeardown    = false;

    /**
     * {@inheritdoc}
     */
    public function getBasePath(): ?string
    {
        return $this->basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function setBasePath(string $basePath): VerifierConfigInterface
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStateChangeUrl(): ?UriInterface
    {
        return $this->stateChangeUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setStateChangeUrl(UriInterface $stateChangeUrl): VerifierConfigInterface
    {
        $this->stateChangeUrl = $stateChangeUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderName(): ?string
    {
        return $this->providerName;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderName(string $providerName): VerifierConfigInterface
    {
        $this->providerName = $providerName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderVersion(): string
    {
        return $this->providerVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderVersion(string $providerVersion): VerifierConfigInterface
    {
        $this->providerVersion = $providerVersion;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderTags(): array
    {
        return $this->providerTags;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderTags(array $providerTags): VerifierConfigInterface
    {
        $this->providerTags = $providerTags;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderBranch(): ?string
    {
        return $this->providerBranch;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderBranch(string $providerBranch): VerifierConfigInterface
    {
        $this->providerBranch = $providerBranch;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublishResults(): bool
    {
        return $this->publishResults;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishResults(bool $publishResults): VerifierConfigInterface
    {
        $this->publishResults = $publishResults;

        return $this;
    }

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
     * @param bool $stateChangeAsQuery
     *
     * @return VerifierConfigInterface
     */
    public function setStateChangeAsQuery(bool $stateChangeAsQuery): VerifierConfigInterface
    {
        $this->stateChangeAsQuery = $stateChangeAsQuery;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStateChangeAsQuery(): bool
    {
        return $this->stateChangeAsQuery;
    }

    /**
     * @param bool $stateChangeTeardown
     *
     * @return VerifierConfigInterface
     */
    public function setStateChangeTeardown(bool $stateChangeTeardown): VerifierConfigInterface
    {
        $this->stateChangeTeardown = $stateChangeTeardown;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStateChangeTeardown(): bool
    {
        return $this->stateChangeTeardown;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuildUrl(): ?UriInterface
    {
        return $this->buildUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setBuildUrl(UriInterface $buildUrl): VerifierConfigInterface
    {
        $this->buildUrl = $buildUrl;

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

    /**
     * {@inheritdoc}
     */
    public function setFilterConsumerNames(string ...$filterConsumerNames): VerifierConfigInterface
    {
        $this->filterConsumerNames = $filterConsumerNames;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterConsumerNames(): array
    {
        return $this->filterConsumerNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterDescription(): ?string
    {
        return $this->filterDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterDescription(string $filterDescription): VerifierConfigInterface
    {
        $this->filterDescription = $filterDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterNoState(): bool
    {
        return $this->filterNoState;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterNoState(bool $filterNoState): VerifierConfigInterface
    {
        $this->filterNoState = $filterNoState;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterState(): ?string
    {
        return $this->filterState;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterState(string $filterState): VerifierConfigInterface
    {
        $this->filterState = $filterState;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function setScheme(string $scheme): VerifierConfigInterface
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost(string $host): VerifierConfigInterface
    {
        $this->host = $host;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function setPort(int $port): VerifierConfigInterface
    {
        $this->port = $port;

        return $this;
    }
}
