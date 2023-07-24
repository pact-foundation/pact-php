<?php

namespace PhpPact\Consumer\Driver\Pact;

use Composer\Semver\Comparator;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Exception\PactFileNotWroteException;
use PhpPact\Consumer\Registry\Pact\PactRegistryInterface;
use PhpPact\FFI\ClientInterface;

class PactDriver implements PactDriverInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected PactConfigInterface $config,
        protected PactRegistryInterface $pactRegistry
    ) {
    }

    public function cleanUp(): void
    {
        $this->pactRegistry->deletePact();
    }

    public function writePact(): void
    {
        $error = $this->client->call(
            'pactffi_pact_handle_write_file',
            $this->pactRegistry->getId(),
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
        );
        if ($error) {
            throw new PactFileNotWroteException($error);
        }
    }

    public function setUp(): void
    {
        $this
            ->initWithLogLevel()
            ->registerPact();
    }

    protected function getSpecification(): int
    {
        return match (true) {
            $this->versionEqualTo('1.0.0') => $this->client->get('PactSpecification_V1'),
            $this->versionEqualTo('1.1.0') => $this->client->get('PactSpecification_V1_1'),
            $this->versionEqualTo('2.0.0') => $this->client->get('PactSpecification_V2'),
            $this->versionEqualTo('3.0.0') => $this->client->get('PactSpecification_V3'),
            $this->versionEqualTo('4.0.0') => $this->client->get('PactSpecification_V4'),
            default => function () {
                trigger_error(sprintf("Specification version '%s' is unknown", $this->config->getPactSpecificationVersion()), E_USER_WARNING);

                return $this->client->get('PactSpecification_Unknown');
            },
        };
    }

    private function versionEqualTo(string $version): bool
    {
        return Comparator::equalTo($this->config->getPactSpecificationVersion(), $version);
    }

    private function initWithLogLevel(): self
    {
        $logLevel = $this->config->getLogLevel();
        if ($logLevel) {
            $this->client->call('pactffi_init_with_log_level', $logLevel);
        }

        return $this;
    }

    private function registerPact(): self
    {
        $this->pactRegistry->registerPact(
            $this->config->getConsumer(),
            $this->config->getProvider(),
            $this->getSpecification()
        );

        return $this;
    }
}
