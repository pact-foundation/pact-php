<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use PhpPact\Standalone\ProviderVerifier\Model\Config\CallingAppInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ConsumerFiltersInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\CustomHeadersInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\FilterInfoInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderInfoInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderStateInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransportInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\PublishOptionsInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\VerificationOptionsInterface;

interface VerifierConfigInterface
{
    public function setCallingApp(CallingAppInterface $callingApp): self;

    public function getCallingApp(): CallingAppInterface;

    public function setProviderInfo(ProviderInfoInterface $providerInfo): self;

    public function getProviderInfo(): ProviderInfoInterface;

    /**
     * @param array<int, ProviderTransportInterface> $providerTransports
     */
    public function setProviderTransports(array $providerTransports): self;

    public function addProviderTransport(ProviderTransportInterface $providerTransport): self;

    /**
     * @return array<int, ProviderTransportInterface>
     */
    public function getProviderTransports(): array;

    public function setFilterInfo(FilterInfoInterface $filterInfo): self;

    public function getFilterInfo(): FilterInfoInterface;

    public function setProviderState(ProviderStateInterface $providerState): self;

    public function getProviderState(): ProviderStateInterface;

    public function setPublishOptions(?PublishOptionsInterface $publishOptions): self;

    public function getPublishOptions(): ?PublishOptionsInterface;

    public function isPublishResults(): bool;

    public function setConsumerFilters(ConsumerFiltersInterface $consumerFilters): self;

    public function getConsumerFilters(): ConsumerFiltersInterface;

    public function setVerificationOptions(VerificationOptionsInterface $verificationOptions): self;

    public function getVerificationOptions(): VerificationOptionsInterface;

    public function getLogLevel(): ?string;

    public function setLogLevel(string $logLevel): self;

    public function setCustomHeaders(CustomHeadersInterface $customHeaders): self;

    public function getCustomHeaders(): CustomHeadersInterface;
}
