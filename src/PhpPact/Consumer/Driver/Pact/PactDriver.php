<?php

namespace PhpPact\Consumer\Driver\Pact;

use Composer\Semver\Comparator;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Exception\PactFileNotWroteException;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\FFI\ClientInterface;

class PactDriver implements PactDriverInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected PactConfigInterface $config
    ) {
    }

    public function deletePact(Pact $pact): void
    {
        $this->client->call('pactffi_free_pact_handle', $pact->handle);
    }

    public function writePact(Pact $pact): void
    {
        $error = $this->client->call(
            'pactffi_pact_handle_write_file',
            $pact->handle,
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
        );
        if ($error) {
            throw new PactFileNotWroteException($error);
        }
    }

    public function newPact(): Pact
    {
        $pact = new Pact($this->client->call('pactffi_new_pact', $this->config->getConsumer(), $this->config->getProvider()));
        $this->client->call('pactffi_with_specification', $pact->handle, $this->getSpecification());

        return $pact;
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

    public function initWithLogLevel(): void
    {
        $logLevel = $this->config->getLogLevel();
        if ($logLevel) {
            $this->client->call('pactffi_init_with_log_level', $logLevel);
        }
    }
}
