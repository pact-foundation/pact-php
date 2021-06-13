<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Source;

use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;

/**
 * Class Broker.
 */
class Broker extends Url implements BrokerInterface
{
    protected bool $enablePending     = false;
    protected ?string $wipPactSince   = null;
    private array $providerTags       = [];
    protected ?string $providerBranch = null;
    protected ConsumerVersionSelectors $consumerVersionSelectors;
    private array $consumerVersionTags = [];

    public function __construct()
    {
        $this->consumerVersionSelectors = new ConsumerVersionSelectors();
    }

    /**
     * {@inheritdoc}
     */
    public function isEnablePending(): bool
    {
        return $this->enablePending;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnablePending(bool $enablePending): BrokerInterface
    {
        $this->enablePending = $enablePending;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludeWipPactSince(string $date): BrokerInterface
    {
        $this->wipPactSince = $date;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIncludeWipPactSince(): ?string
    {
        return $this->wipPactSince;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderTags(): array
    {
        return $this->providerTags;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderTags(array $providerTags): BrokerInterface
    {
        $this->providerTags = $providerTags;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderBranch(): string
    {
        return $this->providerBranch;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderBranch(string $providerBranch): BrokerInterface
    {
        $this->providerBranch = $providerBranch;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumerVersionSelectors(): ConsumerVersionSelectors
    {
        return $this->consumerVersionSelectors;
    }

    /**
     * {@inheritdoc}
     */
    public function setConsumerVersionSelectors(ConsumerVersionSelectors $selectors): BrokerInterface
    {
        $this->consumerVersionSelectors = $selectors;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumerVersionTags(): array
    {
        return $this->consumerVersionTags;
    }

    /**
     * {@inheritdoc}
     */
    public function setConsumerVersionTags(array $consumerVersionTags): BrokerInterface
    {
        $this->consumerVersionTags = $consumerVersionTags;

        return $this;
    }
}
