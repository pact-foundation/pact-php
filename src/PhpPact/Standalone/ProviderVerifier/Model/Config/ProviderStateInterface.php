<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use Psr\Http\Message\UriInterface;

interface ProviderStateInterface
{
    public function getStateChangeUrl(): ?UriInterface;

    public function setStateChangeUrl(?UriInterface $stateChangeUrl): self;

    public function setStateChangeAsBody(bool $stateChangeAsBody): self;

    public function isStateChangeAsBody(): bool;

    public function setStateChangeTeardown(bool $stateChangeTeardown): self;

    public function isStateChangeTeardown(): bool;
}
