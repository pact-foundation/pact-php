<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use Psr\Http\Message\UriInterface;

/**
 * Interface ProviderStateInterface.
 */
interface ProviderStateInterface
{
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
}
