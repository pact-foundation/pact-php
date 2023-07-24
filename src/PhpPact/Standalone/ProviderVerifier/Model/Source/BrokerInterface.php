<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Source;

use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;

interface BrokerInterface extends UrlInterface
{
    public function setEnablePending(bool $enablePending): self;

    public function isEnablePending(): bool;

    /**
     * @param string $date Includes pact marked as WIP since this date.
     *                     Accepted formats: Y-m-d (2020-01-30) or c (ISO 8601 date 2004-02-12T15:19:21+00:00)
     */
    public function setIncludeWipPactSince(?string $date): self;

    public function getIncludeWipPactSince(): ?string;

    /**
     * @return array<string>
     */
    public function getProviderTags(): array;

    /**
     * @param array<string> $providerTags
     */
    public function setProviderTags(array $providerTags): self;

    public function addProviderTag(string $providerTag): self;

    public function getProviderBranch(): ?string;

    public function setProviderBranch(?string $providerBranch): self;

    public function getConsumerVersionSelectors(): ConsumerVersionSelectors;

    public function setConsumerVersionSelectors(ConsumerVersionSelectors $selectors): self;

    /**
     * @return array<string>
     */
    public function getConsumerVersionTags(): array;

    /**
     * @param array<string> $consumerVersionTags
     */
    public function setConsumerVersionTags(array $consumerVersionTags): self;

    public function addConsumerVersionTag(string $consumerVersionTag): self;
}
