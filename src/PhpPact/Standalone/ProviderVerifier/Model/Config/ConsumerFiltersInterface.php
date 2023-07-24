<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

interface ConsumerFiltersInterface
{
    /**
     * @param array<string> $filterConsumerNames
     */
    public function setFilterConsumerNames(array $filterConsumerNames): self;

    public function addFilterConsumerName(string $filterConsumerName): self;

    /**
     * @return array<string>
     */
    public function getFilterConsumerNames(): array;
}
