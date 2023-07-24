<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Source;

use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;

class Broker extends Url implements BrokerInterface
{
    protected bool $enablePending     = false;
    protected ?string $wipPactSince   = null;
    /**
     * @var array<int, string>
     */
    private array $providerTags       = [];
    protected ?string $providerBranch = null;
    protected ConsumerVersionSelectors $consumerVersionSelectors;
    /**
     * @var array<int, string>
     */
    private array $consumerVersionTags = [];

    public function __construct()
    {
        $this->consumerVersionSelectors = new ConsumerVersionSelectors();
    }

    public function isEnablePending(): bool
    {
        return $this->enablePending;
    }

    public function setEnablePending(bool $enablePending): self
    {
        $this->enablePending = $enablePending;

        return $this;
    }

    public function setIncludeWipPactSince(?string $date): self
    {
        $this->wipPactSince = $date;

        return $this;
    }

    public function getIncludeWipPactSince(): ?string
    {
        return $this->wipPactSince;
    }

    public function getProviderTags(): array
    {
        return $this->providerTags;
    }

    public function setProviderTags(array $providerTags): self
    {
        $this->providerTags = [];
        foreach ($providerTags as $providerTag) {
            $this->addProviderTag($providerTag);
        }

        return $this;
    }

    public function addProviderTag(string $providerTag): self
    {
        $this->providerTags[] = $providerTag;

        return $this;
    }

    public function getProviderBranch(): ?string
    {
        return $this->providerBranch;
    }

    public function setProviderBranch(?string $providerBranch): self
    {
        $this->providerBranch = $providerBranch;

        return $this;
    }

    public function getConsumerVersionSelectors(): ConsumerVersionSelectors
    {
        return $this->consumerVersionSelectors;
    }

    public function setConsumerVersionSelectors(ConsumerVersionSelectors $selectors): self
    {
        $this->consumerVersionSelectors = $selectors;

        return $this;
    }

    public function getConsumerVersionTags(): array
    {
        return $this->consumerVersionTags;
    }

    public function setConsumerVersionTags(array $consumerVersionTags): self
    {
        $this->consumerVersionTags = [];
        foreach ($consumerVersionTags as $consumerVersionTag) {
            $this->addConsumerVersionTag($consumerVersionTag);
        }

        return $this;
    }

    public function addConsumerVersionTag(string $consumerVersionTag): self
    {
        $this->consumerVersionTags[] = $consumerVersionTag;

        return $this;
    }
}
