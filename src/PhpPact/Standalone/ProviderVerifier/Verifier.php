<?php

namespace PhpPact\Standalone\ProviderVerifier;

use PhpPact\Broker\Service\BrokerHttpServiceInterface;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Wrapper for the Ruby Standalone Verifier service.
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

    public function __construct(VerifierConfigInterface $config, BrokerHttpServiceInterface $brokerHttpService)
    {
        $this->config            = $config;
        $this->brokerHttpService = $brokerHttpService;
        $this->installManager    = new InstallManager();
    }

    /**
     * @return array parameters to be passed into the process
     */
    public function getArguments(): array
    {
        $parameters = [];

        if ($this->config->getProviderBaseUrl() !== null) {
            $parameters[] = "--provider-base-url={$this->config->getProviderBaseUrl()}";
        }

        if ($this->config->getProviderVersion() !== null) {
            $parameters[] = "--provider-app-version={$this->config->getProviderVersion()}";
        }

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
     * Make the request to the PACT Verifier Service to run a Pact file tests from the Pact Broker.
     *
     * @param string      $consumerName name of the consumer to be compared against
     * @param null|string $tag          optional tag of the consumer such as a branch name
     *
     * @return self
     */
    public function verify(string $consumerName, string $tag = null): self
    {
        // Add tag if set.
        if ($tag !== null) {
            $uri = $this->config->getBrokerUri()
                ->withPath("pacts/provider/{$this->config->getProviderName()}/consumer/{$consumerName}/latest/{$tag}");
        } else {
            $uri = $this->config->getBrokerUri()
                ->withPath("pacts/provider/{$this->config->getProviderName()}/consumer/{$consumerName}/latest");
        }

        $arguments = \array_merge([$uri->__toString()], $this->getArguments());

        $this->verifyAction($arguments);

        return $this;
    }

    /**
     * Provides a way to validate local Pact JSON files.
     *
     * @param array $files paths to pact json files
     *
     * @return self
     */
    public function verifyFiles(array $files): self
    {
        $arguments = \array_merge([$files], $this->getArguments());

        $this->verifyAction($arguments);

        return $this;
    }

    /**
     * Verify all Pacts from the Pact Broker are valid for the Provider.
     */
    public function verifyAll()
    {
        $arguments = $this->brokerHttpService->getAllConsumerUrls($this->config->getProviderName(), $this->config->getProviderVersion());

        $arguments = \array_merge($arguments, $this->getArguments());

        $this->verifyAction($arguments);
    }

    /**
     * Wrapper to add a custom installer.
     *
     * @param InstallerInterface $installer
     *
     * @return self
     */
    public function registerInstaller(InstallerInterface $installer): self
    {
        $this->installManager->registerInstaller($installer);

        return $this;
    }

    /**
     * Execute the Pact Verifier Service.
     *
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
