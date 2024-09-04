<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use PhpPact\Config\LogLevelTrait;
use PhpPact\Standalone\ProviderVerifier\Model\Config\CallingApp;
use PhpPact\Standalone\ProviderVerifier\Model\Config\CallingAppInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ConsumerFilters;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ConsumerFiltersInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\CustomHeaders;
use PhpPact\Standalone\ProviderVerifier\Model\Config\CustomHeadersInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\FilterInfo;
use PhpPact\Standalone\ProviderVerifier\Model\Config\FilterInfoInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderInfo;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderInfoInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderState;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderStateInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransportInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\PublishOptionsInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\VerificationOptions;
use PhpPact\Standalone\ProviderVerifier\Model\Config\VerificationOptionsInterface;

class VerifierConfig implements VerifierConfigInterface
{
    use LogLevelTrait;

    private CallingAppInterface $callingApp;
    private ProviderInfoInterface $providerInfo;

    /**
     * @var array<int, ProviderTransportInterface>
     */
    private array $providerTransports = [];

    private FilterInfoInterface $filterInfo;
    private ProviderStateInterface $providerState;
    private VerificationOptionsInterface $verificationOptions;
    private ?PublishOptionsInterface $publishOptions = null;
    private ConsumerFiltersInterface $consumerFilters;
    private CustomHeadersInterface $customHeaders;

    public function __construct()
    {
        $this->callingApp = new CallingApp();
        $this->providerInfo = new ProviderInfo();
        $this->filterInfo = new FilterInfo();
        $this->providerState = new ProviderState();
        $this->verificationOptions = new VerificationOptions();
        $this->consumerFilters = new ConsumerFilters();
        $this->customHeaders = new CustomHeaders();
    }

    public function setCallingApp(CallingAppInterface $callingApp): self
    {
        $this->callingApp = $callingApp;

        return $this;
    }

    public function getCallingApp(): CallingAppInterface
    {
        return $this->callingApp;
    }

    public function setProviderInfo(ProviderInfoInterface $providerInfo): self
    {
        $this->providerInfo = $providerInfo;

        return $this;
    }

    public function getProviderInfo(): ProviderInfoInterface
    {
        return $this->providerInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderTransports(array $providerTransports): self
    {
        $this->providerTransports = [];
        foreach ($providerTransports as $providerTransport) {
            $this->addProviderTransport($providerTransport);
        }

        return $this;
    }

    public function addProviderTransport(ProviderTransportInterface $providerTransport): self
    {
        $this->providerTransports[] = $providerTransport;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderTransports(): array
    {
        return $this->providerTransports;
    }

    public function setFilterInfo(FilterInfoInterface $filterInfo): self
    {
        $this->filterInfo = $filterInfo;

        return $this;
    }

    public function getFilterInfo(): FilterInfoInterface
    {
        return $this->filterInfo;
    }

    public function setProviderState(ProviderStateInterface $providerState): self
    {
        $this->providerState = $providerState;

        return $this;
    }

    public function getProviderState(): ProviderStateInterface
    {
        return $this->providerState;
    }

    public function setPublishOptions(?PublishOptionsInterface $publishOptions): self
    {
        $this->publishOptions = $publishOptions;

        return $this;
    }

    public function getPublishOptions(): ?PublishOptionsInterface
    {
        return $this->publishOptions;
    }

    public function isPublishResults(): bool
    {
        return $this->publishOptions !== null;
    }

    public function setConsumerFilters(ConsumerFiltersInterface $consumerFilters): self
    {
        $this->consumerFilters = $consumerFilters;

        return $this;
    }

    public function getConsumerFilters(): ConsumerFiltersInterface
    {
        return $this->consumerFilters;
    }

    public function setVerificationOptions(VerificationOptionsInterface $verificationOptions): self
    {
        $this->verificationOptions = $verificationOptions;

        return $this;
    }

    public function getVerificationOptions(): VerificationOptionsInterface
    {
        return $this->verificationOptions;
    }

    public function setCustomHeaders(CustomHeadersInterface $customHeaders): self
    {
        $this->customHeaders = $customHeaders;

        return $this;
    }

    public function getCustomHeaders(): CustomHeadersInterface
    {
        return $this->customHeaders;
    }
}
