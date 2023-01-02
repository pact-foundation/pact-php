<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;

/**
 * Trait FilterInfo.
 */
trait FilterInfo
{
    private ?string $filterDescription = null;
    private bool $filterNoState        = false;
    private ?string $filterState       = null;

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
}
