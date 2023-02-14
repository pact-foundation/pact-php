<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use Psr\Http\Message\UriInterface;

/**
 * {@inheritdoc}
 */
class VerifierConfig implements VerifierConfigInterface
{
    private ?UriInterface $providerBaseUrl = null;

    private ?string $providerStatesSetupUrl = null;

    private ?string $providerName = null;

    private ?string $providerVersion = null;

    private ?string $providerBranch;

    /**
     * @var array<int, string>
     */
    private array $providerVersionTag = [];

    private bool $publishResults = false;

    private ?UriInterface $brokerUri = null;

    private ?string $brokerToken = null;

    private ?string $brokerUsername = null;

    private ?string $brokerPassword = null;

    /**
     * @var array<int, string>
     */
    private array $customProviderHeaders = [];

    private bool $verbose = false;

    private ?string $logDirectory = null;

    private string $format;

    private int $processTimeout = 60;

    private int $processIdleTimeout = 10;

    private bool $enablePending = false;

    private ?string $wipPactSince = null;

    /**
     * @var array<int, string>
     */
    private array $consumerVersionTag = [];

    private ConsumerVersionSelectors $consumerVersionSelectors;

    /** @var null|callable */
    private $requestFilter;

    public function __construct()
    {
        $this->consumerVersionSelectors = new ConsumerVersionSelectors();
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderBaseUrl(): ?UriInterface
    {
        return $this->providerBaseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderBaseUrl(UriInterface $providerBaseUrl): VerifierConfigInterface
    {
        $this->providerBaseUrl = $providerBaseUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderStatesSetupUrl(): ?string
    {
        return $this->providerStatesSetupUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderStatesSetupUrl(string $providerStatesSetupUrl): VerifierConfigInterface
    {
        $this->providerStatesSetupUrl = $providerStatesSetupUrl;

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
    public function getProviderVersion(): ?string
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
    public function getProviderVersionTag(): array
    {
        return $this->providerVersionTag;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderVersionTag(string $providerVersionTag): VerifierConfigInterface
    {
        return $this->addProviderVersionTag($providerVersionTag);
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumerVersionTag(): array
    {
        return $this->consumerVersionTag;
    }

    /**
     * {@inheritdoc}
     */
    public function addConsumerVersionTag(string $consumerVersionTag): VerifierConfigInterface
    {
        $this->consumerVersionTag[] = $consumerVersionTag;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addProviderVersionTag(string $providerVersionTag): VerifierConfigInterface
    {
        $this->providerVersionTag[] = $providerVersionTag;

        return $this;
    }

    /**
     * @param string $consumerVersionTag
     *
     * @return VerifierConfigInterface
     */
    public function setConsumerVersionTag(string $consumerVersionTag): VerifierConfigInterface
    {
        return $this->addConsumerVersionTag($consumerVersionTag);
    }

    public function getConsumerVersionSelectors(): ConsumerVersionSelectors
    {
        return $this->consumerVersionSelectors;
    }

    public function setConsumerVersionSelectors(ConsumerVersionSelectors $selectors): VerifierConfigInterface
    {
        $this->consumerVersionSelectors = $selectors;

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
    public function getBrokerUri(): ?UriInterface
    {
        return $this->brokerUri;
    }

    /**
     * {@inheritdoc}
     */
    public function setBrokerUri(UriInterface $brokerUri): VerifierConfigInterface
    {
        $this->brokerUri = $brokerUri;

        return $this;
    }

    /**
     * {@inheritdoc}}
     */
    public function getBrokerToken(): ?string
    {
        return $this->brokerToken;
    }

    /**
     * {@inheritdoc }
     */
    public function setBrokerToken(?string $brokerToken): VerifierConfigInterface
    {
        $this->brokerToken = $brokerToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrokerUsername(): ?string
    {
        return $this->brokerUsername;
    }

    /**
     * {@inheritdoc}
     */
    public function setBrokerUsername(string $brokerUsername): VerifierConfigInterface
    {
        $this->brokerUsername = $brokerUsername;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrokerPassword(): ?string
    {
        return $this->brokerPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function setBrokerPassword(string $brokerPassword): self
    {
        $this->brokerPassword = $brokerPassword;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomProviderHeaders(): array
    {
        return $this->customProviderHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomProviderHeaders(array $customProviderHeaders): VerifierConfigInterface
    {
        $this->customProviderHeaders = $customProviderHeaders;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomProviderHeader(string $name, string $value): VerifierConfigInterface
    {
        $this->customProviderHeaders[] = "{$name}: {$value}";

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * {@inheritdoc}
     */
    public function setVerbose(bool $verbose): VerifierConfigInterface
    {
        $this->verbose = $verbose;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDirectory(): ?string
    {
        return $this->logDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogDirectory(string $log): VerifierConfigInterface
    {
        $this->logDirectory = $log;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormat(string $format): VerifierConfigInterface
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param int $timeout
     *
     * @return VerifierConfigInterface
     */
    public function setProcessTimeout(int $timeout): VerifierConfigInterface
    {
        $this->processTimeout = $timeout;

        return $this;
    }

    /**
     * @param int $timeout
     *
     * @return VerifierConfigInterface
     */
    public function setProcessIdleTimeout(int $timeout): VerifierConfigInterface
    {
        $this->processIdleTimeout = $timeout;

        return $this;
    }

    /**
     * @return int
     */
    public function getProcessTimeout(): int
    {
        return $this->processTimeout;
    }

    /**
     * @return int
     */
    public function getProcessIdleTimeout(): int
    {
        return $this->processIdleTimeout;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnablePending(): bool
    {
        return $this->enablePending;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnablePending(bool $pending): VerifierConfigInterface
    {
        $this->enablePending = $pending;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludeWipPactSince(string $date): VerifierConfigInterface
    {
        $this->wipPactSince = $date;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIncludeWipPactSince(): ?string
    {
        return $this->wipPactSince;
    }

    public function getRequestFilter(): ?callable
    {
        return $this->requestFilter;
    }

    public function setRequestFilter(callable $requestFilter): VerifierConfigInterface
    {
        $this->requestFilter = $requestFilter;

        return $this;
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
    public function getProviderBranch(): ?string
    {
        return $this->providerBranch;
    }
}
