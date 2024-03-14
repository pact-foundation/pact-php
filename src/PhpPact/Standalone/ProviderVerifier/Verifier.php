<?php

namespace PhpPact\Standalone\ProviderVerifier;

use FFI\CData;
use PhpPact\FFI\Client;
use PhpPact\FFI\ClientInterface;
use PhpPact\FFI\Model\ArrayData;
use PhpPact\Service\LoggerInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Source\BrokerInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Source\UrlInterface;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;

class Verifier
{
    protected ClientInterface $client;
    protected CData $handle;

    public function __construct(VerifierConfigInterface $config, private ?LoggerInterface $logger = null, ?ClientInterface $client = null)
    {
        $this->client = $client ?? new Client();
        $this->newHandle($config);
        $this->setProviderInfo($config);
        $this->setProviderTransports($config);
        $this->setFilterInfo($config);
        $this->setProviderState($config);
        $this->setVerificationOptions($config);
        $this->setPublishOptions($config);
        $this->setConsumerFilters($config);
        $this->setLogLevel($config);
    }

    private function newHandle(VerifierConfigInterface $config): void
    {
        $this->handle = $this->client->call(
            'pactffi_verifier_new_for_application',
            $config->getCallingApp()->getName(),
            $config->getCallingApp()->getVersion()
        );
    }

    private function setProviderInfo(VerifierConfigInterface $config): void
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
    }

    private function setProviderTransports(VerifierConfigInterface $config): void
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
    }

    private function setFilterInfo(VerifierConfigInterface $config): void
    {
        $this->client->call(
            'pactffi_verifier_set_filter_info',
            $this->handle,
            $config->getFilterInfo()->getFilterDescription(),
            $config->getFilterInfo()->getFilterState(),
            $config->getFilterInfo()->getFilterNoState()
        );
    }

    private function setProviderState(VerifierConfigInterface $config): void
    {
        $this->client->call(
            'pactffi_verifier_set_provider_state',
            $this->handle,
            $config->getProviderState()->getStateChangeUrl() ? (string) $config->getProviderState()->getStateChangeUrl() : null,
            $config->getProviderState()->isStateChangeTeardown(),
            $config->getProviderState()->isStateChangeAsBody()
        );
    }

    private function setVerificationOptions(VerifierConfigInterface $config): void
    {
        $this->client->call(
            'pactffi_verifier_set_verification_options',
            $this->handle,
            $config->getVerificationOptions()->isDisableSslVerification(),
            $config->getVerificationOptions()->getRequestTimeout()
        );
    }

    private function setPublishOptions(VerifierConfigInterface $config): void
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
    }

    private function setConsumerFilters(VerifierConfigInterface $config): void
    {
        $filterConsumerNames = ArrayData::createFrom($config->getConsumerFilters()->getFilterConsumerNames());
        $this->client->call(
            'pactffi_verifier_set_consumer_filters',
            $this->handle,
            $filterConsumerNames?->getItems(),
            $filterConsumerNames?->getSize()
        );
    }

    private function setLogLevel(VerifierConfigInterface $config): void
    {
        if ($logLevel = $config->getLogLevel()) {
            $this->client->call('pactffi_init_with_log_level', $logLevel);
        }
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
        if ($this->logger) {
            $this->logger->log($this->client->call('pactffi_verifier_json', $this->handle));
        }
        $this->client->call('pactffi_verifier_shutdown', $this->handle);

        return !$error;
    }
}
