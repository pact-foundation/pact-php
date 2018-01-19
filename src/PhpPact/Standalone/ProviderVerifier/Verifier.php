<?php

namespace PhpPact\Standalone\ProviderVerifier;

use PhpPact\Broker\Service\BrokerHttpServiceInterface;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Verify
 * Class VerifierServer
 * @package PhpPact\Standalone\Provider
 */
class Verifier
{
    /** @var VerifierConfigInterface */
    private $config;

    /** @var BrokerHttpServiceInterface */
    private $brokerHttpService;

    /** @var InstallManager */
    private $installManager;

    public function __construct(VerifierConfigInterface $config, BrokerHttpServiceInterface $brokerHttpService, InstallManager $installManager)
    {
        $this->config            = $config;
        $this->brokerHttpService = $brokerHttpService;
        $this->installManager = $installManager;
    }

    /**
     * @return array parameters to be passed into the process
     */
    public function getParameters(): array
    {
        // Get the URLs of the Pact Files from the broker.
        $parameters = $this->brokerHttpService->getAllConsumerUrls($this->config->getProviderName(), $this->config->getProviderVersion());

        // Required Parameters
        $parameters[] = "--provider-base-url={$this->config->getProviderBaseUrl()}";
        $parameters[] = "--provider-app-version={$this->config->getProviderVersion()}";

        // Optional Parameters
        if ($this->config->getProviderStatesSetupUrl() !== null) {
            $parameters[] = "--provider-states-setup-url={$this->config->getProviderStatesSetupUrl()}";
        }

        if ($this->config->isPublishResults() === true) {
            $parameters[] = '--publish-verification-results';
        }

        if ($this->config->getBrokerUsername() !== null) {
            $parameters[] = "--broker-username={$this->config->getBrokerUsername()}";
        }

        if ($this->config->getBrokerPassword() !== null) {
            $parameters[] = "--broker-password={$this->config->getBrokerPassword()}";
        }

        if ($this->config->getCustomProviderHeaders() !== null) {
            foreach ($this->config->getCustomProviderHeaders() as $customProviderHeader) {
                $parameters[] = "--custom-provider-header={$customProviderHeader}";
            }
        }

        if ($this->config->isVerbose() === true) {
            $parameters[] = '--verbose';
        }

        if ($this->config->getFormat() !== null) {
            $parameters[] = "--format={$this->config->getFormat()}";
        }

        return $parameters;
    }

    public function verify()
    {
        $scripts = $this->installManager->install();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($scripts->getProviderVerifier())
            ->setArguments($this->getParameters())
            ->getProcess()
            ->setTimeout(600)
            ->setIdleTimeout(60);

        $process->mustRun(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > '.$buffer;
            } else {
                echo 'OUT > '.$buffer;
            }
        });
    }
}
