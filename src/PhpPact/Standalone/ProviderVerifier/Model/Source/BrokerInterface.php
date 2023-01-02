<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Source;

use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;

/**
 * Interface BrokerInterface.
 */
interface BrokerInterface extends UrlInterface
{
    /**
     * @param bool $enablePending
     *
     * @return $this
     */
    public function setEnablePending(bool $enablePending): self;

    /**
     * @return bool
     */
    public function isEnablePending(): bool;

    /**
     * @param string $date Includes pact marked as WIP since this date.
     *                     Accepted formats: Y-m-d (2020-01-30) or c (ISO 8601 date 2004-02-12T15:19:21+00:00)
     *
     * @return $this
     */
    public function setIncludeWipPactSince(string $date): self;

    /**
     * @return null|string
     */
    public function getIncludeWipPactSince(): ?string;

    /**
     * @return array
     */
    public function getProviderTags(): array;

    /**
     * @param array|string[] $providerTags
     *
     * @return $this
     */
    public function setProviderTags(array $providerTags): self;

    /**
     * @return string
     */
    public function getProviderBranch(): string;

    /**
     * @param string $providerBranch
     *
     * @return $this
     */
    public function setProviderBranch(string $providerBranch): self;

    /**
     * @return ConsumerVersionSelectors
     */
    public function getConsumerVersionSelectors(): ConsumerVersionSelectors;

    /**
     * @param ConsumerVersionSelectors $selectors
     *
     * @return $this
     */
    public function setConsumerVersionSelectors(ConsumerVersionSelectors $selectors): self;

    /**
     * @return array
     */
    public function getConsumerVersionTags(): array;

    /**
     * @param array $consumerVersionTags
     *
     * @return $this
     */
    public function setConsumerVersionTags(array $consumerVersionTags): self;
}
