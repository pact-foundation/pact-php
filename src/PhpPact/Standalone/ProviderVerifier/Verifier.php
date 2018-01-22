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
        $this->installManager    = $installManager;
    }

    /**
     * @return array parameters to be passed into the process
     */
    public function getArguments(): array
    {
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

    /**
     * Make the request to the PACT Verifier Service to run the tests.
     * @param string $consumerName
     * @param string $tag
     * @return self
     */
    public function verify(string $consumerName, string $tag): self
    {
        $uri = $this->config->getBrokerUri()
            ->withPath("pacts/provider/{$this->config->getProviderName()}/consumer/{$consumerName}/latest/{$tag}")
            ->__toString();

        $arguments = array_merge([$uri], $this->getArguments());

        $this->verifyAction($arguments);

        return $this;
    }

    /**
     * Verify all Pacts for the Provider are valid.
     */
    public function verifyAll()
    {
        $arguments = $this->brokerHttpService->getAllConsumerUrls($this->config->getProviderName(), $this->config->getProviderVersion());

        $arguments = array_merge($arguments, $this->getArguments());

        $this->verifyAction($arguments);
    }

    /**
     * Execute the Pact Verifier Service.
     * @param array $arguments
     */
    private function verifyAction(array $arguments)
    {
        $scripts = $this->installManager->install();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($scripts->getProviderVerifier())
            ->setArguments($arguments)
            ->getProcess()
            ->setTimeout(60)
            ->setIdleTimeout(10);

        $process->mustRun(function ($type, $buffer) {
            if (Process::ERR === $type) {
                print 'ERR > ' . $buffer;
            } else {
                print 'OUT > ' . $buffer;
            }
        });
    }
}
