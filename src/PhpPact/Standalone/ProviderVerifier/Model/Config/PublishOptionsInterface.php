<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use Psr\Http\Message\UriInterface;

interface PublishOptionsInterface
{
    /**
     * @return array<string>
     */
    public function getProviderTags(): array;

    /**
     * @param array<string> $providerTags
     */
    public function setProviderTags(array $providerTags): self;

    public function addProviderTag(string $providerTag): self;

    public function getProviderVersion(): string;

    public function setProviderVersion(string $providerVersion): self;

    public function getBuildUrl(): ?UriInterface;

    public function setBuildUrl(UriInterface $buildUrl): self;

    public function getProviderBranch(): ?string;

    public function setProviderBranch(?string $providerBranch): self;
}
