<?php

namespace PhpPact\Standalone\ProviderVerifier;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PhpPact\Broker\Service\BrokerHttpClient;
use PhpPact\Broker\Service\BrokerHttpClientInterface;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;

/**
 * Wrapper for the Ruby Standalone Verifier service.
 * Class VerifierServer.
 */
class Verifier
{
    /** @var int */
    protected $processTimeout = 60;

    /** @var int */
    protected $processIdleTimeout = 10;

    /** @var VerifierConfigInterface */
    protected $config;

    /** @var null|BrokerHttpClientInterface */
    protected $brokerHttpClient;

    /** @var InstallManager */
    protected $installManager;

    /** @var null|VerifierProcess */
    protected $verifierProcess;

    public function __construct(
        VerifierConfigInterface $config,
        InstallManager $installManager = null,
        VerifierProcess $verifierProcess = null,
        BrokerHttpClient $brokerHttpClient = null
    ) {
        $this->config             = $config;
        $this->installManager     = $installManager ?: new InstallManager();
        $this->verifierProcess    = $verifierProcess ?: new VerifierProcess($this->installManager);
        $this->processTimeout     = $config->getProcessTimeout();
        $this->processIdleTimeout = $config->getProcessIdleTimeout();

        if ($brokerHttpClient) {
            $this->brokerHttpClient = $brokerHttpClient;
        }
    }

    /**
     * @throws \Exception
     *
     * @return array parameters to be passed into the process
     */
    public function getArguments(): array
    {
        $parameters = [];

        if (!empty($this->config->getProviderName())) {
            $parameters[] = "--provider='{$this->config->getProviderName()}'";
        }

        if ($this->config->getProviderBaseUrl() !== null) {
            $parameters[] = "--provider-base-url={$this->config->getProviderBaseUrl()}";
        }

        if ($this->config->getProviderVersion() !== null) {
            $parameters[] = "--provider-app-version={$this->config->getProviderVersion()}";
        }

        if ($this->config->getProviderBranch() !== null) {
            $parameters[] = "--provider-version-branch={$this->config->getProviderBranch()}";
        }

        if (\count($this->config->getConsumerVersionTag()) > 0) {
            foreach ($this->config->getConsumerVersionTag() as $tag) {
                $parameters[] = "--consumer-version-tag={$tag}";
            }
        }

        if (\count($this->config->getConsumerVersionSelectors()) > 0) {
            foreach ($this->config->getConsumerVersionSelectors() as $selector) {
                $parameters[] = "--consumer-version-selector='{$selector}'";
            }
        }

        if (\count($this->config->getProviderVersionTag()) > 0) {
            foreach ($this->config->getProviderVersionTag() as $tag) {
                $parameters[] = "--provider-version-tag={$tag}";
            }
        }

        if ($this->config->getProviderStatesSetupUrl() !== null) {
            $parameters[] = "--provider-states-setup-url={$this->config->getProviderStatesSetupUrl()}";
        }

        if ($this->config->isPublishResults() === true) {
            $parameters[] = '--publish-verification-results';
        }

        if ($this->config->getBrokerToken() !== null) {
            $parameters[] = "--broker-token={$this->config->getBrokerToken()}";
        }

        if ($this->config->getBrokerUsername() !== null) {
            $parameters[] = "--broker-username={$this->config->getBrokerUsername()}";
        }

        if ($this->config->getBrokerPassword() !== null) {
            $parameters[] = "--broker-password={$this->config->getBrokerPassword()}";
        }

        if ($this->config->getCustomProviderHeaders() !== null) {
            foreach ($this->config->getCustomProviderHeaders() as $customProviderHeader) {
                $parameters[] = "--custom-provider-header=\"{$customProviderHeader}\"";
            }
        }

        if ($this->config->isVerbose() === true) {
            $parameters[] = '--verbose';
        }

        if ($this->config->getLogDirectory() !== null) {
            $parameters[] = "--log={$this->config->getLogDirectory()}";
        }

        if ($this->config->getFormat() !== null) {
            $parameters[] = "--format={$this->config->getFormat()}";
        }

        if ($this->config->isEnablePending() === true) {
            $parameters[] = '--enable-pending';
        }

        if ($this->config->getIncludeWipPactSince() !== null) {
            $parameters[] = "--include-wip-pacts-since={$this->config->getIncludeWipPactSince()}";
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
        $path = "/pacts/provider/{$this->config->getProviderName()}/consumer/{$consumerName}/";

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
        $arguments = $this->getBrokerHttpClient()->getAllConsumerUrls($this->config->getProviderName());

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
     * Verify all PACTs that match the VerifierConfig
     *
     * @throws FileDownloadFailureException
     * @throws NoDownloaderFoundException
     */
    public function verifyFromConfig()
    {
        $this->verifyAction($this->getArguments());
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
     * Trigger execution of the Pact Verifier Service.
     *
     * @param array $arguments
     *
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     */
    protected function verifyAction(array $arguments)
    {
        $this->verifierProcess->run($arguments, $this->processTimeout, $this->processIdleTimeout);
    }

    protected function getBrokerHttpClient(): BrokerHttpClientInterface
    {
        if (!$this->brokerHttpClient) {
            $user      = $this->config->getBrokerUsername();
            $password  = $this->config->getBrokerPassword();
            $token     = $this->config->getBrokerToken();
            $reqFilter = $this->config->getRequestFilter();

            $config = [];
            if (\strlen($token) > 0) {
                $config = ['headers' => ['Authorization' => 'Bearer ' . $token]];
            } elseif ($user && $password) {
                $config = ['auth' => [$user, $password]];
            }
            if (\is_callable($reqFilter)) {
                $stack = HandlerStack::create();
                $stack->push(Middleware::mapRequest($reqFilter), 'requestFilter');
                $config['handler'] = $stack;
            }
            $client = new GuzzleClient($config);

            $this->brokerHttpClient = new BrokerHttpClient($client, $this->config->getBrokerUri());
        }

        return $this->brokerHttpClient;
    }
}
