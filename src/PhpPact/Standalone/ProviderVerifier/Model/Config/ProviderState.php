<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use Psr\Http\Message\UriInterface;

/**
 * Trait ProviderState.
 */
trait ProviderState
{
    private ?UriInterface $stateChangeUrl = null;
    private bool $stateChangeAsBody       = true;
    private bool $stateChangeTeardown     = false;

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
    public function setStateChangeAsBody(bool $stateChangeAsBody): VerifierConfigInterface
    {
        $this->stateChangeAsBody = $stateChangeAsBody;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isStateChangeAsBody(): bool
    {
        return $this->stateChangeAsBody;
    }

    /**
     * {@inheritdoc}
     */
    public function setStateChangeTeardown(bool $stateChangeTeardown): VerifierConfigInterface
    {
        $this->stateChangeTeardown = $stateChangeTeardown;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isStateChangeTeardown(): bool
    {
        return $this->stateChangeTeardown;
    }
}
