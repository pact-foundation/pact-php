<?php

namespace PhpPact\Standalone\ProviderVerifier;

use FFI\CData;
use PhpPact\FFI\Client;
use PhpPact\FFI\ClientInterface;
use PhpPact\FFI\Model\ArrayData;
use PhpPact\Standalone\ProviderVerifier\Model\Source\BrokerInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Source\UrlInterface;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;

class Verifier
{
    protected ClientInterface $client;
    protected CData $handle;

    public function __construct(VerifierConfigInterface $config)
    {
        $this->client = new Client();
        $this
            ->newHandle($config)
            ->setProviderInfo($config)
            ->setProviderTransports($config)
            ->setFilterInfo($config)
            ->setProviderState($config)
            ->setVerificationOptions($config)
            ->setPublishOptions($config)
            ->setConsumerFilters($config)
            ->setLogLevel($config);
    }

    private function newHandle(VerifierConfigInterface $config): self
    {
        $this->handle = $this->client->call(
            'pactffi_verifier_new_for_application',
            $config->getCallingApp()->getName(),
            $config->getCallingApp()->getVersion()
        );

        return $this;
    }

    private function setProviderInfo(VerifierConfigInterface $config): self
    {
        $this->client->call(
            'pactffi_verifier_set_provider_info',
            $this->handle,
            $config->getProviderInfo()->getName(),
            $config->getProviderInfo()->getScheme(),
            $config->getProviderInfo()->getHost(),
            $config->getProviderInfo()->getPort(),
            $config->getProviderInfo()->getPath()
        );

        return $this;
    }

    private function setProviderTransports(VerifierConfigInterface $config): self
    {
        foreach ($config->getProviderTransports() as $transport) {
            $this->client->call(
                'pactffi_verifier_add_provider_transport',
                $this->handle,
                $transport->getProtocol(),
                $transport->getPort(),
                $transport->getPath(),
                $transport->getScheme()
            );
        }

        return $this;
    }

    private function setFilterInfo(VerifierConfigInterface $config): self
    {
        $this->client->call(
            'pactffi_verifier_set_provider_state',
            $this->handle,
            $config->getProviderState()->getStateChangeUrl() ? (string) $config->getProviderState()->getStateChangeUrl() : null,
            $config->getProviderState()->isStateChangeTeardown(),
            $config->getProviderState()->isStateChangeAsBody()
        );

        return $this;
    }

    private function setProviderState(VerifierConfigInterface $config): self
    {
        $this->client->call(
            'pactffi_verifier_set_filter_info',
            $this->handle,
            $config->getFilterInfo()->getFilterDescription(),
            $config->getFilterInfo()->getFilterState(),
            $config->getFilterInfo()->getFilterNoState()
        );

        return $this;
    }

    private function setVerificationOptions(VerifierConfigInterface $config): self
    {
        $this->client->call(
            'pactffi_verifier_set_verification_options',
            $this->handle,
            $config->getVerificationOptions()->isDisableSslVerification(),
            $config->getVerificationOptions()->getRequestTimeout()
        );

        return $this;
    }

    private function setPublishOptions(VerifierConfigInterface $config): self
    {
        if ($config->isPublishResults()) {
            $providerTags = ArrayData::createFrom($config->getPublishOptions()->getProviderTags());
            $this->client->call(
                'pactffi_verifier_set_publish_options',
                $this->handle,
                $config->getPublishOptions()->getProviderVersion(),
                $config->getPublishOptions()->getBuildUrl(),
                $providerTags?->getItems(),
                $providerTags?->getSize(),
                $config->getPublishOptions()->getProviderBranch()
            );
        }

        return $this;
    }

    private function setConsumerFilters(VerifierConfigInterface $config): self
    {
        $filterConsumerNames = ArrayData::createFrom($config->getConsumerFilters()->getFilterConsumerNames());
        $this->client->call(
            'pactffi_verifier_set_consumer_filters',
            $this->handle,
            $filterConsumerNames?->getItems(),
            $filterConsumerNames?->getSize()
        );

        return $this;
    }

    private function setLogLevel(VerifierConfigInterface $config): self
    {
        if ($logLevel = $config->getLogLevel()) {
            $this->client->call('pactffi_init_with_log_level', $logLevel);
        }

        return $this;
    }

    public function addFile(string $file): self
    {
        $this->client->call('pactffi_verifier_add_file_source', $this->handle, $file);

        return $this;
    }

    public function addDirectory(string $directory): self
    {
        $this->client->call('pactffi_verifier_add_directory_source', $this->handle, $directory);

        return $this;
    }

    public function addUrl(UrlInterface $url): self
    {
        $this->client->call(
            'pactffi_verifier_url_source',
            $this->handle,
            (string) $url->getUrl(),
            $url->getUsername(),
            $url->getPassword(),
            $url->getToken()
        );

        return $this;
    }

    public function addBroker(BrokerInterface $broker): self
    {
        $providerTags = ArrayData::createFrom($broker->getProviderTags());
        $consumerVersionSelectors = ArrayData::createFrom(iterator_to_array($broker->getConsumerVersionSelectors()));
        $consumerVersionTags = ArrayData::createFrom($broker->getConsumerVersionTags());
        $this->client->call(
            'pactffi_verifier_broker_source_with_selectors',
            $this->handle,
            (string) $broker->getUrl(),
            $broker->getUsername(),
            $broker->getPassword(),
            $broker->getToken(),
            $broker->isEnablePending(),
            $broker->getIncludeWipPactSince(),
            $providerTags?->getItems(),
            $providerTags?->getSize(),
            $broker->getProviderBranch(),
            $consumerVersionSelectors?->getItems(),
            $consumerVersionSelectors?->getSize(),
            $consumerVersionTags?->getItems(),
            $consumerVersionTags?->getSize()
        );

        return $this;
    }

    public function verify(): bool
    {
        $error = $this->client->call('pactffi_verifier_execute', $this->handle);
        $this->client->call('pactffi_verifier_shutdown', $this->handle);

        return !$error;
    }
}
