<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

/**
 * Interface ConsumerFiltersInterface.
 */
interface ConsumerFiltersInterface
{
    /**
     * @param string ...$filterConsumerNames
     *
     * @return $this
     */
    public function setFilterConsumerNames(string ...$filterConsumerNames): self;

    /**
     * @return array
     */
    public function getFilterConsumerNames(): array;
}
