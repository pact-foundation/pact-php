<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

class ConsumerFilters implements ConsumerFiltersInterface
{
    /**
     * @var array<string>
     */
    private array $filterConsumerNames = [];

    public function setFilterConsumerNames(array $filterConsumerNames): self
    {
        $this->filterConsumerNames = [];
        foreach ($filterConsumerNames as $filterConsumerName) {
            $this->addFilterConsumerName($filterConsumerName);
        }

        return $this;
    }

    public function addFilterConsumerName(string $filterConsumerName): self
    {
        $this->filterConsumerNames[] = $filterConsumerName;

        return $this;
    }

    public function getFilterConsumerNames(): array
    {
        return $this->filterConsumerNames;
    }
}
