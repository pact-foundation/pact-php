<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use Psr\Http\Message\UriInterface;

/**
 * Interface PublishOptionsInterface.
 */
interface PublishOptionsInterface
{
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
     * @return array
     */
    public function getProviderTags(): array;

    /**
     * @param string ...$providerTags
     *
     * @return $this
     */
    public function setProviderTags(string ...$providerTags): self;

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
     * @return string|null
     */
    public function getProviderBranch(): ?string;

    /**
     * @param string $providerBranch
     *
     * @return $this
     */
    public function setProviderBranch(string $providerBranch): self;
}
