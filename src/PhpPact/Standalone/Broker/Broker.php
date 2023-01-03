<?php

namespace PhpPact\Standalone\Broker;

use Exception;
use PhpPact\Standalone\Installer\Model\Scripts;
use Symfony\Component\Process\Process;

class Broker
{
    private BrokerConfig $config;

    private string $command;

    public function __construct(BrokerConfig $config)
    {
        $this->config  = $config;
        $this->command = Scripts::getBroker();
    }

    public function canIDeploy(): array
    {
        return $this->runThenDecodeJson([
            'can-i-deploy',
            '--pacticipant', $this->config->getPacticipant(),
            '--version', $this->config->getVersion(),
        ]);
    }

    /**
     * @return array<int, string> parameters to be passed into the process
     */
    public function getArguments(): array
    {
        $parameters = [];

        if ($this->config->getBrokerUri() !== null) {
            $parameters[] = '--broker-base-url';
            $parameters[] = $this->config->getBrokerUri();
        }

        if ($this->config->getBrokerToken() !== null) {
            $parameters[] = '--broker-token';
            $parameters[] = $this->config->getBrokerToken();
        }

        if ($this->config->getBrokerUsername() !== null) {
            $parameters[] = '--broker-username';
            $parameters[] = $this->config->getBrokerUsername();
        }

        if ($this->config->getBrokerPassword() !== null) {
            $parameters[] = '--broker-password';
            $parameters[] = $this->config->getBrokerPassword();
        }

        return $parameters;
    }

    public function createOrUpdatePacticipant(): array
    {
        return $this->runThenDecodeJson([
            'create-or-update-pacticipant',
            '--name', $this->config->getName(),
            '--repository-url', $this->config->getRepositoryUrl(),
        ]);
    }

    public function createOrUpdateWebhook(): array
    {
        return $this->runThenDecodeJson([
            'create-or-update-webhook',
            $this->config->getUrl(),
            '--request', $this->config->getRequest(),
            '--header', $this->config->getHeader(),
            '--data', $this->config->getData(),
            '--user', $this->config->getUser(),
            '--consumer', $this->config->getConsumer(),
            '--provider', $this->config->getProvider(),
            '--description', $this->config->getDescription(),
            '--uuid', $this->config->getUuid(),
        ]);
    }

    public function createVersionTag(): array
    {
        return $this->runThenDecodeJson([
            'create-version-tag',
            '--pacticipant', $this->config->getPacticipant(),
            '--version', $this->config->getVersion(),
            '--tag', $this->config->getTag(),
        ]);
    }

    public function createWebhook(): array
    {
        return $this->runThenDecodeJson([
            'create-webhook',
            $this->config->getUrl(),
            '--request', $this->config->getRequest(),
            '--header', $this->config->getHeader(),
            '--data', $this->config->getData(),
            '--user', $this->config->getUser(),
            '--consumer', $this->config->getConsumer(),
            '--provider', $this->config->getProvider(),
            '--description', $this->config->getDescription(),
        ]);
    }

    public function describeVersion(): array
    {
        return $this->runThenDecodeJson([
            'describe-version',
            '--pacticipant', $this->config->getPacticipant(),
            '--output', 'json',
        ]);
    }

    public function listLatestPactVersions(): array
    {
        return $this->runThenDecodeJson([
            'list-latest-pact-versions',
            '--output', 'json',
        ]);
    }

    public function publish(): void
    {
        $options = [
            'publish',
            $this->config->getPactLocations(),
            '--consumer-app-version', $this->config->getConsumerVersion(),
        ];

        if (null !== $this->config->getBranch()) {
            $options[] = '--branch';
            $options[] = $this->config->getBranch();
        }

        if (null !== $this->config->getTag()) {
            $options[] = '--tag';
            $options[] = $this->config->getTag();
        }

        $this->run($options);
    }

    public function testWebhook(): array
    {
        return $this->runThenDecodeJson([
            'test-webhook',
            '--uuid', $this->config->getUuid(),
        ]);
    }

    public function generateUuid(): string
    {
        return \rtrim($this->run(['generate-uuid']));
    }

    private function run(array $options): string
    {
        $process = new Process([
            $this->command,
            ...$options,
            ...$this->getArguments(),
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new Exception("PactPHP Process returned non-zero exit code: {$process->getExitCode()}", $process->getExitCode());
        }

        return $process->getOutput();
    }

    private function runThenDecodeJson(array $options): array
    {
        return \json_decode($this->run($options), true, 512, JSON_THROW_ON_ERROR);
    }
}
