<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use Psr\Http\Message\UriInterface;

/**
 * {@inheritdoc}
 */
trait PublishOptions
{
    private bool $publishResults    = false;
    private array $providerTags     = [];
    private string $providerVersion;
    private ?UriInterface $buildUrl = null;
    private ?string $providerBranch = null;

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
    public function getProviderTags(): array
    {
        return $this->providerTags;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderTags(string ...$providerTags): VerifierConfigInterface
    {
        $this->providerTags = $providerTags;

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
}
