<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

/**
 * Interface FilterInfoInterface.
 */
interface FilterInfoInterface
{
    /**
     * @param string $filterDescription
     *
     * @return $this
     */
    public function setFilterDescription(string $filterDescription): self;

    /**
     * @return null|string
     */
    public function getFilterDescription(): ?string;

    /**
     * @param bool $filterNoState
     *
     * @return $this
     */
    public function setFilterNoState(bool $filterNoState): self;

    /**
     * @return bool
     */
    public function getFilterNoState(): bool;

    /**
     * @param string $filterState
     *
     * @return $this
     */
    public function setFilterState(string $filterState): self;

    /**
     * @return null|string
     */
    public function getFilterState(): ?string;
}
