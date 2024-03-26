<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use Psr\Http\Message\UriInterface;

class ProviderState implements ProviderStateInterface
{
    private ?UriInterface $stateChangeUrl = null;
    private bool $stateChangeAsBody       = true;
    private bool $stateChangeTeardown     = false;

    public function getStateChangeUrl(): ?UriInterface
    {
        return $this->stateChangeUrl;
    }

    public function setStateChangeUrl(?UriInterface $stateChangeUrl): self
    {
        $this->stateChangeUrl = $stateChangeUrl;

        return $this;
    }

    public function setStateChangeAsBody(bool $stateChangeAsBody): self
    {
        $this->stateChangeAsBody = $stateChangeAsBody;

        return $this;
    }

    public function isStateChangeAsBody(): bool
    {
        return $this->stateChangeAsBody;
    }

    public function setStateChangeTeardown(bool $stateChangeTeardown): self
    {
        $this->stateChangeTeardown = $stateChangeTeardown;

        return $this;
    }

    public function isStateChangeTeardown(): bool
    {
        return $this->stateChangeTeardown;
    }
}
