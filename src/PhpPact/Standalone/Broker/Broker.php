<?php

namespace PhpPact\Standalone\Broker;

use PhpPact\Standalone\Installer\Model\Scripts;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Broker
{
    /** @var BrokerConfig */
    private BrokerConfig $config;
    /** @var string */
    private string $command;

    public function __construct(BrokerConfig $config)
    {
        $this->config  = $config;
        $this->command = Scripts::getBroker();
    }

    public function canIDeploy(): array
    {
        return $this->run([
            'can-i-deploy',
            '--pacticipant=' . $this->config->getPacticipant(),
            '--version=' . $this->config->getVersion()
        ]);
    }

    /**
     * @return array parameters to be passed into the process
     */
    public function getArguments(): array
    {
        $parameters = [];

        if ($this->config->getBrokerUri() !== null) {
            $parameters[] = "--broker-base-url={$this->config->getBrokerUri()}";
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

        return $parameters;
    }

    public function createOrUpdatePacticipant(): array
    {
        return $this->run([
            'create-or-update-pacticipant',
            '--name=' . $this->config->getName(),
            '--repository-url=' . $this->config->getRepositoryUrl(),
        ]);
    }

    public function createOrUpdateWebhook(): array
    {
        return $this->run([
            'create-or-update-webhook',
            $this->config->getUrl(),
            '--request=' . $this->config->getRequest(),
            '--header=' . $this->config->getHeader(),
            '--data=' . $this->config->getData(),
            '--user=' . $this->config->getUser(),
            '--consumer=' . $this->config->getConsumer(),
            '--provider=' . $this->config->getProvider(),
            '--description=' . $this->config->getDescription(),
            '--uuid=' . $this->config->getUuid(),
        ]);
    }

    public function createVersionTag(): array
    {
        return $this->run([
            'create-version-tag',
            '--pacticipant=' . $this->config->getPacticipant(),
            '--version=' . $this->config->getVersion(),
            '--tag=' . $this->config->getTag(),
        ]);
    }

    public function createWebhook(): array
    {
        return $this->run([
            'create-webhook',
            $this->config->getUrl(),
            '--request=' . $this->config->getRequest(),
            '--header=' . $this->config->getHeader(),
            '--data=' . $this->config->getData(),
            '--user=' . $this->config->getUser(),
            '--consumer=' . $this->config->getConsumer(),
            '--provider=' . $this->config->getProvider(),
            '--description=' . $this->config->getDescription(),
        ]);
    }

    public function describeVersion(): array
    {
        return $this->run([
            'describe-version',
            '--pacticipant=' . $this->config->getPacticipant(),
            '--output=json',
        ]);
    }

    public function listLatestPactVersions(): array
    {
        return $this->run([
            'list-latest-pact-versions',
            '--output=json',
        ]);
    }

    public function publish(): void
    {
        $options = [
            'publish',
            $this->config->getPactLocations(),
            '--consumer-app-version=' . $this->config->getConsumerVersion(),
        ];

        if (null !== $this->config->getBranch()) {
            $options[] = '--branch=' . $this->config->getBranch();
        }

        if (null !== $this->config->getTag()) {
            $options[] = '--tag=' . $this->config->getTag();
        }

        $process = new Process([
            $this->command,
            ...$options,
            ...$this->getArguments(),
        ]);

        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                fputs(STDERR, $buffer);
            } else {
                fputs(STDOUT, $buffer);
            }
        });
    }

    public function testWebhook(): array
    {
        return $this->run([
            'test-webhook',
            '--uuid=' . $this->config->getUuid(),
        ]);
    }

    public function generateUuid(): string
    {
        $process = new Process([$this->command, 'generate-uuid']);
        $process->run();

        return \rtrim($process->getOutput());
    }

    protected function run(array $options): array
    {
        $process = new Process([
            $this->command,
            ...$options,
            ...$this->getArguments(),
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return \json_decode($process->getOutput(), true);
    }
}
