<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

class FilterInfo implements FilterInfoInterface
{
    private ?string $filterDescription = null;
    private bool $filterNoState        = false;
    private ?string $filterState       = null;

    public function getFilterDescription(): ?string
    {
        return $this->filterDescription;
    }

    public function setFilterDescription(?string $filterDescription): self
    {
        $this->filterDescription = $filterDescription;

        return $this;
    }

    public function getFilterNoState(): bool
    {
        return $this->filterNoState;
    }

    public function setFilterNoState(bool $filterNoState): self
    {
        $this->filterNoState = $filterNoState;

        return $this;
    }

    public function getFilterState(): ?string
    {
        return $this->filterState;
    }

    public function setFilterState(?string $filterState): self
    {
        $this->filterState = $filterState;

        return $this;
    }
}
