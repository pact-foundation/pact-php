<?php

namespace PhpPact\Standalone\Broker;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\Runner\ProcessRunner;

class Broker
{
    /** @var Logger */
    private $logger;
    /** @var BrokerConfig */
    private $config;
    /** @var string */
    private $command;

    public function __construct(BrokerConfig $config)
    {
        $this->config  = $config;
        $this->command = Scripts::getBroker();
        $this->logger  = (new Logger('console'))
            ->pushHandler(
                (new StreamHandler(new ResourceOutputStream(\STDOUT)))
                    ->setFormatter(new ConsoleFormatter(null, null, true))
            );
    }

    public function canIDeploy()
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'can-i-deploy',
                    '--pacticipant=' . $this->config->getPacticipant(),
                    '--version=' . $this->config->getVersion()
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        if ($runner->getExitCode() !== 0) {
            throw new \Exception($runner->getStderr());
        }

        return \json_decode($runner->getOutput(), true);
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

    public function createOrUpdatePacticipant()
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

        if ($runner->getExitCode() !== 0) {
            throw new \Exception($runner->getStderr());
        }

        return \json_decode($runner->getOutput(), true);
    }

    public function createOrUpdateWebhook()
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

        if ($runner->getExitCode() !== 0) {
            throw new \Exception($runner->getStderr());
        }

        return \json_decode($runner->getOutput(), true);
    }

    public function createVersionTag()
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'create-version-tag',
                    '--pacticipant=' . $this->config->getPacticipant(),
                    '--version=' . $this->config->getVersion(),
                    '--tag=' . $this->config->getTag(),
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        if ($runner->getExitCode() !== 0) {
            throw new \Exception($runner->getStderr());
        }

        return \json_decode($runner->getOutput(), true);
    }

    public function createWebhook()
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

        if ($runner->getExitCode() !== 0) {
            throw new \Exception($runner->getStderr());
        }

        return \json_decode($runner->getOutput(), true);
    }

    public function describeVersion()
    {
        $runner = new ProcessRunner(
            $this->command,
            \array_merge(
                [
                    'describe-version',
                    '--pacticipant=' . $this->config->getPacticipant(),
                    '--output=json',
                ],
                $this->getArguments()
            )
        );
        $runner->runBlocking();

        if ($runner->getExitCode() !== 0) {
            throw new \Exception($runner->getStderr());
        }

        return \json_decode($runner->getOutput(), true);
    }

    public function listLatestPactVersions()
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

        if ($runner->getExitCode() !== 0) {
            throw new \Exception($runner->getStderr());
        }

        return \json_decode($runner->getOutput(), true);
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

        try {
            $runner->runBlocking();

            $this->logger->info('out > ' . $runner->getOutput());
            $this->logger->error('err > ' . $runner->getStderr());
        } catch (\Exception $e) {
            $this->logger->info('out > ' . $runner->getOutput());
            $this->logger->error('err > ' . $runner->getStderr());

            throw $e;
        }
    }

    public function testWebhook()
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

        if ($runner->getExitCode() !== 0) {
            throw new \Exception($runner->getStderr());
        }

        return \json_decode($runner->getOutput(), true);
    }

    public function generateUuid(): string
    {
        $runner = new ProcessRunner($this->command, ['generate-uuid']);
        $runner->runBlocking();

        return \rtrim($runner->getOutput());
    }
}
