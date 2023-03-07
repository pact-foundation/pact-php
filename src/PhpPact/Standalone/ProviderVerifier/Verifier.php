<?php

namespace PhpPact\Standalone\ProviderVerifier;

use FFI;
use FFI\CData;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\ProviderVerifier\Model\ArrayData;
use PhpPact\Standalone\ProviderVerifier\Model\Source\BrokerInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Source\UrlInterface;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;

/**
 * Class Verifier.
 */
class Verifier
{
    protected FFI $ffi;
    protected CData $handle;

    /**
     * Verifier constructor.
     */
    public function __construct(VerifierConfigInterface $config)
    {
        $this->ffi = FFI::cdef(\file_get_contents(Scripts::getHeader()), Scripts::getLibrary());
        $this->ffi->pactffi_init('PACT_LOGLEVEL');
        $this->newHandle($config);
    }

    /**
     * @param VerifierConfigInterface $config
     *
     * @return $this
     */
    private function newHandle(VerifierConfigInterface $config): void
    {
        $this->handle = $this->ffi->pactffi_verifier_new();
        $this->ffi->pactffi_verifier_set_provider_info(
            $this->handle,
            $config->getProviderName(),
            $config->getScheme(),
            $config->getHost(),
            $config->getPort(),
            $config->getBasePath()
        );
        $this->ffi->pactffi_verifier_set_filter_info(
            $this->handle,
            $config->getFilterDescription(),
            $config->getFilterState(),
            $config->getFilterNoState()
        );
        $this->ffi->pactffi_verifier_set_provider_state(
            $this->handle,
            $config->getStateChangeUrl() ? (string) $config->getStateChangeUrl() : null,
            $config->isStateChangeTeardown(),
            !$config->isStateChangeAsQuery()
        );
        $this->ffi->pactffi_verifier_set_verification_options(
            $this->handle,
            $config->isDisableSslVerification(),
            $config->getRequestTimeout()
        );
        if ($config->isPublishResults()) {
            $providerTags = ArrayData::createFrom($config->getProviderTags());
            $this->ffi->pactffi_verifier_set_publish_options(
                $this->handle,
                $config->getProviderVersion(),
                $config->getBuildUrl(),
                $providerTags->getItems(),
                $providerTags->getSize(),
                $config->getProviderBranch()
            );
        }
        $filterConsumerNames = ArrayData::createFrom($config->getFilterConsumerNames());
        $this->ffi->pactffi_verifier_set_consumer_filters(
            $this->handle,
            $filterConsumerNames->getItems(),
            $filterConsumerNames->getSize()
        );
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function addFile(string $file): self
    {
        $this->ffi->pactffi_verifier_add_file_source($this->handle, $file);

        return $this;
    }

    /**
     * @param string $directory
     *
     * @return $this
     */
    public function addDirectory(string $directory): self
    {
        $this->ffi->pactffi_verifier_add_directory_source($this->handle, $directory);

        return $this;
    }

    /**
     * @param UrlInterface $url
     *
     * @return $this
     */
    public function addUrl(UrlInterface $url): self
    {
        $this->ffi->pactffi_verifier_url_source(
            $this->handle,
            (string) $url->getUrl(),
            $url->getUsername(),
            $url->getPassword(),
            $url->getToken()
        );

        return $this;
    }

    /**
     * @param BrokerInterface $broker
     *
     * @return $this
     */
    public function addBroker(BrokerInterface $broker): self
    {
        $providerTags = ArrayData::createFrom($broker->getProviderTags());
        $consumerVersionSelectors = ArrayData::createFrom($broker->getConsumerVersionSelectors());
        $consumerVersionTags = ArrayData::createFrom($broker->getConsumerVersionTags());
        $this->ffi->pactffi_verifier_broker_source_with_selectors(
            $this->handle,
            (string) $broker->getUrl(),
            $broker->getUsername(),
            $broker->getPassword(),
            $broker->getToken(),
            $broker->isEnablePending(),
            $broker->getIncludeWipPactSince(),
            $providerTags->getItems(),
            $providerTags->getSize(),
            $broker->getProviderBranch(),
            $consumerVersionSelectors->getItems(),
            $consumerVersionSelectors->getSize(),
            $consumerVersionTags->getItems(),
            $consumerVersionTags->getSize()
        );

        return $this;
    }

    /**
     * Verifier a provider.
     *
     * @return bool
     */
    public function verify(): bool
    {
        $error = $this->ffi->pactffi_verifier_execute($this->handle);
        $this->ffi->pactffi_verifier_shutdown($this->handle);

        return !$error;
    }
}
