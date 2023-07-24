<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use Psr\Http\Message\UriInterface;

class PublishOptions implements PublishOptionsInterface
{
    /**
     * @var array<string>
     */
    private array $providerTags     = [];
    private string $providerVersion;
    private ?UriInterface $buildUrl = null;
    private ?string $providerBranch = null;

    public function getProviderTags(): array
    {
        return $this->providerTags;
    }

    public function setProviderTags(array $providerTags): self
    {
        $this->providerTags = [];
        foreach ($providerTags as $providerTag) {
            $this->addProviderTag($providerTag);
        }

        return $this;
    }

    public function addProviderTag(string $providerTag): self
    {
        $this->providerTags[] = $providerTag;

        return $this;
    }

    public function getProviderVersion(): string
    {
        return $this->providerVersion;
    }

    public function setProviderVersion(string $providerVersion): self
    {
        $this->providerVersion = $providerVersion;

        return $this;
    }

    public function getBuildUrl(): ?UriInterface
    {
        return $this->buildUrl;
    }

    public function setBuildUrl(?UriInterface $buildUrl): self
    {
        $this->buildUrl = $buildUrl;

        return $this;
    }

    public function getProviderBranch(): ?string
    {
        return $this->providerBranch;
    }

    public function setProviderBranch(?string $providerBranch): self
    {
        $this->providerBranch = $providerBranch;

        return $this;
    }
}
