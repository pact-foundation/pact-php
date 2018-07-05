<?php

namespace PhpPact\Standalone\ProviderVerifier;

use PhpPact\Broker\Service\BrokerHttpClient;
use PhpPact\Broker\Service\BrokerHttpClientInterface;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\Process;

/**
 * Wrapper for the Ruby Standalone Verifier service.
 * Class VerifierServer.
 */
class Verifier
{
    /** @var int $processTimeout */
    private $processTimeout = 60;

    /** @var int $processIdleTimeout */
    private $processIdleTimeout = 10;

    /** @var VerifierConfigInterface */
    private $config;

    /** @var BrokerHttpClientInterface */
    private $brokerHttpClient;

    /** @var InstallManager */
    private $installManager;

    /** @var ConsoleOutput */
    private $console;

    public function __construct(VerifierConfigInterface $config)
    {
        $this->config             = $config;
        $this->installManager     = new InstallManager();
        $this->console            = new ConsoleOutput();
        $this->processTimeout     = $config->getProcessTimeout();
        $this->processIdleTimeout = $config->getProcessIdleTimeout();
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
     * @param string      $consumerName    name of the consumer to be compared against
     * @param null|string $tag             optional tag of the consumer such as a branch name
     * @param null|string $consumerVersion optional specific version of the consumer; this is overridden by tag
     *
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     *
     * @return Verifier
     */
    public function verify(string $consumerName, string $tag = null, string $consumerVersion = null): self
    {
        $path = "pacts/provider/{$this->config->getProviderName()}/consumer/{$consumerName}/";

        if ($tag) {
            $path .= "latest/{$tag}/";
        } elseif ($consumerVersion) {
            $path .= "version/{$consumerVersion}/";
        } else {
            $path .= 'latest/';
        }

        $uri = $this->config->getBrokerUri()->withPath($path);

        $arguments = \array_merge([$uri->__toString()], $this->getArguments());

        $this->verifyAction($arguments);

        return $this;
    }

    /**
     * Provides a way to validate local Pact JSON files.
     *
     * @param array $files paths to pact json files
     *
     * @throws FileDownloadFailureException
     * @throws NoDownloaderFoundException
     *
     * @return Verifier
     */
    public function verifyFiles(array $files): self
    {
        $arguments = \array_merge($files, $this->getArguments());

        $this->verifyAction($arguments);

        return $this;
    }

    /**
     * Verify all Pacts from the Pact Broker are valid for the Provider.
     *
     * @throws FileDownloadFailureException
     * @throws NoDownloaderFoundException
     */
    public function verifyAll()
    {
        $arguments = $this->getBrokerHttpClient()->getAllConsumerUrls($this->config->getProviderName(), $this->config->getProviderVersion());

        $arguments = \array_merge($arguments, $this->getArguments());

        $this->verifyAction($arguments);
    }

    /**
     * Verify all PACTs for a given tag.
     *
     * @param string $tag
     *
     * @throws FileDownloadFailureException
     * @throws NoDownloaderFoundException
     */
    public function verifyAllForTag(string $tag)
    {
        $arguments = $this->getBrokerHttpClient()->getAllConsumerUrlsForTag($this->config->getProviderName(), $tag);

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

    public function getTimeoutValues(): array
    {
        return ['process_timeout' => $this->processTimeout, 'process_idle_timeout' => $this->processIdleTimeout];
    }

    /**
     * Execute the Pact Verifier Service.
     *
     * @param array $arguments
     *
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     */
    private function verifyAction(array $arguments)
    {
        $scripts = $this->installManager->install();

        $arguments = \array_merge([$scripts->getProviderVerifier()], $arguments);

        $process = new Process($arguments, null, null, null, $this->processTimeout);
        $process->setIdleTimeout($this->processIdleTimeout);

        $this->console->write("Verifying PACT with script {$process->getCommandLine()}");

        $process->mustRun(function ($type, $buffer) {
            $this->console->write("{$type} > {$buffer}");
        });
    }

    private function getBrokerHttpClient(): BrokerHttpClient
    {
        if (!$this->brokerHttpClient) {
            $this->brokerHttpClient = new BrokerHttpClient(new GuzzleClient(), $this->config->getBrokerUri());
        }

        return $this->brokerHttpClient;
    }
}
