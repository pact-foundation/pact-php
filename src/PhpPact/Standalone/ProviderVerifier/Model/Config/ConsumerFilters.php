<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;

/**
 * Trait ConsumerFilters.
 */
trait ConsumerFilters
{
    private array $filterConsumerNames = [];

    /**
     * {@inheritdoc}
     */
    public function setFilterConsumerNames(string ...$filterConsumerNames): VerifierConfigInterface
    {
        $this->filterConsumerNames = $filterConsumerNames;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterConsumerNames(): array
    {
        return $this->filterConsumerNames;
    }
}
