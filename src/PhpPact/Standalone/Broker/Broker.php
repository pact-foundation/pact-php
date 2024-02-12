<?php

namespace PhpPact\Standalone\Broker;

use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\Runner\ProcessRunner;

class Broker
{
    private BrokerConfig $config;

    private string $command;

    public function __construct(BrokerConfig $config)
    {
        $this->config  = $config;
        $this->command = Scripts::getBroker();
    }

    /**
     * @throws \Exception
     */
    public function canIDeploy(): mixed
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'can-i-deploy',
                    '--pacticipant=\'' . $this->config->getPacticipant().'\'',
                    '--version=' . $this->config->getVersion(),
                    '--output=json',
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        return \json_decode($runner->getOutput(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<int, string> parameters to be passed into the process
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

    /**
     * @throws \Exception
     */
    public function createOrUpdatePacticipant(): mixed
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'create-or-update-pacticipant',
                    '--name=' . $this->config->getName(),
                    '--repository-url=' . $this->config->getRepositoryUrl(),
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        return \json_decode($runner->getOutput(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \Exception
     */
    public function createOrUpdateWebhook(): mixed
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
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
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        return \json_decode($runner->getOutput(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \Exception
     */
    public function createVersionTag(): mixed
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'create-version-tag',
                    '--pacticipant=\'' . $this->config->getPacticipant().'\'',
                    '--version=' . $this->config->getVersion(),
                    '--tag=' . $this->config->getTag(),
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        return \json_decode($runner->getOutput(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \Exception
     */
    public function createWebhook(): mixed
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'create-webhook',
                    $this->config->getUrl(),
                    '--request=' . $this->config->getRequest(),
                    '--header=' . $this->config->getHeader(),
                    '--data=' . $this->config->getData(),
                    '--user=' . $this->config->getUser(),
                    '--consumer=' . $this->config->getConsumer(),
                    '--provider=' . $this->config->getProvider(),
                    '--description=' . $this->config->getDescription(),
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        return \json_decode($runner->getOutput(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \Exception
     */
    public function describeVersion(): mixed
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'describe-version',
                    '--pacticipant=\'' . $this->config->getPacticipant().'\'',
                    '--output=json',
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        return \json_decode($runner->getOutput(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \Exception
     */
    public function listLatestPactVersions(): mixed
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'list-latest-pact-versions',
                    '--output=json',
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        return \json_decode($runner->getOutput(), true, 512, JSON_THROW_ON_ERROR);
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

        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                $options,
                $this->getArguments()
            )
        );

        $runner->runBlocking();
    }

    /**
     * @throws \Exception
     */
    public function testWebhook(): mixed
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'test-webhook',
                    '--uuid=' . $this->config->getUuid(),
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        return \json_decode($runner->getOutput(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function generateUuid(): string
    {
        $runner = new ProcessRunner($this->command, ['generate-uuid']);
        $runner->runBlocking();

        return \rtrim($runner->getOutput());
    }
}
