<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

interface FilterInfoInterface
{
    public function setFilterDescription(?string $filterDescription): self;

    public function getFilterDescription(): ?string;

    public function setFilterNoState(bool $filterNoState): self;

    public function getFilterNoState(): bool;

    public function setFilterState(?string $filterState): self;

    public function getFilterState(): ?string;
}
