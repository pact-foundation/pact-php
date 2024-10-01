<?php

namespace PhpPact\Standalone\ProviderVerifier;

use FFI\CData;
use PhpPact\FFI\Client;
use PhpPact\FFI\ClientInterface;
use PhpPact\FFI\Model\ArrayData;
use PhpPact\Service\LoggerInterface;
use PhpPact\Standalone\ProviderVerifier\Exception\InvalidVerifierHandleException;
use PhpPact\Standalone\ProviderVerifier\Exception\InvalidVerifierJsonException;
use PhpPact\Standalone\ProviderVerifier\Exception\VerifierNotCreatedException;
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
        $this->setCustomHeaders($config);
        $this->setLogLevel($config);
    }

    private function newHandle(VerifierConfigInterface $config): void
    {
        $result = $this->client->verifierNewForApplication(
            $config->getCallingApp()->getName(),
            $config->getCallingApp()->getVersion()
        );
        if (!$result) {
            throw new VerifierNotCreatedException();
        }
        $this->handle = $result;
    }

    private function setProviderInfo(VerifierConfigInterface $config): void
    {
        $this->client->verifierSetProviderInfo(
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
            $this->client->verifierAddProviderTransport(
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
        $this->client->verifierSetFilterInfo(
            $this->handle,
            $config->getFilterInfo()->getFilterDescription(),
            $config->getFilterInfo()->getFilterState(),
            $config->getFilterInfo()->getFilterNoState()
        );
    }

    private function setProviderState(VerifierConfigInterface $config): void
    {
        $this->client->verifierSetProviderState(
            $this->handle,
            $config->getProviderState()->getStateChangeUrl() ? (string) $config->getProviderState()->getStateChangeUrl() : null,
            $config->getProviderState()->isStateChangeTeardown(),
            $config->getProviderState()->isStateChangeAsBody()
        );
    }

    private function setVerificationOptions(VerifierConfigInterface $config): void
    {
        $this->client->verifierSetVerificationOptions(
            $this->handle,
            $config->getVerificationOptions()->isDisableSslVerification(),
            $config->getVerificationOptions()->getRequestTimeout()
        );
    }

    private function setPublishOptions(VerifierConfigInterface $config): void
    {
        if ($config->isPublishResults() && $config->getPublishOptions()) {
            $providerTags = ArrayData::createFrom($config->getPublishOptions()->getProviderTags());
            $this->client->verifierSetPublishOptions(
                $this->handle,
                $config->getPublishOptions()->getProviderVersion(),
                $config->getPublishOptions()->getBuildUrl(),
                $providerTags,
                $config->getPublishOptions()->getProviderBranch()
            );
        }
    }

    private function setConsumerFilters(VerifierConfigInterface $config): void
    {
        $filterConsumerNames = ArrayData::createFrom($config->getConsumerFilters()->getFilterConsumerNames());
        $this->client->verifierSetConsumerFilters(
            $this->handle,
            $filterConsumerNames
        );
    }

    private function setCustomHeaders(VerifierConfigInterface $config): void
    {
        foreach ($config->getCustomHeaders()->getHeaders() as $name => $value) {
            $this->client->verifierAddCustomHeader(
                $this->handle,
                $name,
                $value
            );
        }
    }

    private function setLogLevel(VerifierConfigInterface $config): void
    {
        if ($logLevel = $config->getLogLevel()) {
            $this->client->initWithLogLevel($logLevel);
        }
    }

    public function addFile(string $file): self
    {
        $this->client->verifierAddFileSource($this->handle, $file);

        return $this;
    }

    public function addDirectory(string $directory): self
    {
        $this->client->verifierAddDirectorySource($this->handle, $directory);

        return $this;
    }

    public function addUrl(UrlInterface $url): self
    {
        $this->client->verifierAddUrlSource(
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
        $this->client->verifierBrokerSourceWithSelectors(
            $this->handle,
            (string) $broker->getUrl(),
            $broker->getUsername(),
            $broker->getPassword(),
            $broker->getToken(),
            $broker->isEnablePending(),
            $broker->getIncludeWipPactSince(),
            $providerTags,
            $broker->getProviderBranch(),
            $consumerVersionSelectors,
            $consumerVersionTags
        );

        return $this;
    }

    public function verify(): bool
    {
        $error = $this->client->verifierExecute($this->handle);
        if ($this->logger) {
            $output = $this->client->verifierJson($this->handle);
            if (is_null($output)) {
                throw new InvalidVerifierHandleException();
            }
            $this->logger->log($output);
        }
        $this->client->verifierShutdown($this->handle);

        return !$error;
    }
}
