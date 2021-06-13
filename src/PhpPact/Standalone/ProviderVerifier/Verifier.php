<?php

namespace PhpPact\Standalone\ProviderVerifier;

use FFI;
use FFI\CData;
use PhpPact\Ffi\Helper;
use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\Installer\InstallManager;
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
     *
     * @throws FileDownloadFailureException
     * @throws NoDownloaderFoundException
     */
    public function __construct()
    {
        $scripts   = (new InstallManager())->install();
        $this->ffi = FFI::cdef(\file_get_contents($scripts->getCode()), $scripts->getLibrary());
        $this->ffi->pactffi_init('PACT_LOGLEVEL');
    }

    /**
     * @param VerifierConfigInterface $config
     *
     * @return $this
     */
    public function newHandle(VerifierConfigInterface $config): self
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
        $providerTags = Helper::getArray($config->getProviderTags());
        $this->ffi->pactffi_verifier_set_verification_options(
            $this->handle,
            $config->isDisableSslVerification(),
            $config->getRequestTimeout()
        );
        if ($config->isPublishResults()) {
            $this->ffi->pactffi_verifier_set_publish_options(
                $this->handle,
                $config->getProviderVersion(),
                $config->getBuildUrl(),
                $providerTags->getValue(),
                $providerTags->getSize(),
                $config->getProviderBranch()
            );
        }
        $filterConsumerNames = Helper::getArray($config->getFilterConsumerNames());
        $this->ffi->pactffi_verifier_set_consumer_filters(
            $this->handle,
            $filterConsumerNames->getValue(),
            $filterConsumerNames->getSize()
        );

        return $this;
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
            $url->getUrl(),
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
        $providerTags = Helper::getArray($broker->getProviderTags());
        $consumerVersionSelectors = Helper::getArray($broker->getConsumerVersionSelectors());
        $consumerVersionTags = Helper::getArray($broker->getConsumerVersionTags());
        $this->ffi->pactffi_verifier_broker_source_with_selectors(
            $this->handle,
            $broker->getUrl(),
            $broker->getUsername(),
            $broker->getPassword(),
            $broker->getToken(),
            $broker->isEnablePending(),
            $broker->getIncludeWipPactSince(),
            $providerTags->getValue(),
            $providerTags->getSize(),
            $broker->getProviderBranch(),
            $consumerVersionSelectors->getValue(),
            $consumerVersionSelectors->getSize(),
            $consumerVersionTags->getValue(),
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
        $result = $this->ffi->pactffi_verifier_execute($this->handle);
        $this->ffi->pactffi_verifier_shutdown($this->handle);

        return !$result;
    }
}
