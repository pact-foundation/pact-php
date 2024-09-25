<?php

namespace PhpPact\Consumer\Driver\Pact;

use Composer\Semver\Comparator;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Exception\MissingPactException;
use PhpPact\Consumer\Driver\Exception\PactFileNotWrittenException;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\FFI\ClientInterface;

class PactDriver implements PactDriverInterface
{
    protected ?Pact $pact = null;

    public function __construct(
        protected ClientInterface $client,
        protected PactConfigInterface $config
    ) {
    }

    public function cleanUp(): void
    {
        $this->validatePact();
        $this->client->call('pactffi_free_pact_handle', $this->pact->handle);
        $this->pact = null;
    }

    public function writePact(): void
    {
        $this->validatePact();
        $error = $this->client->call(
            'pactffi_pact_handle_write_file',
            $this->pact->handle,
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
        );
        if ($error) {
            throw new PactFileNotWrittenException($error);
        }
    }

    public function getPact(): Pact
    {
        $this->validatePact();

        return $this->pact;
    }

    public function setUp(): void
    {
        if ($this->pact) {
            return;
        }
        $this->initWithLogLevel();
        $this->newPact();
        $this->withSpecification();
    }

    protected function getSpecification(): int
    {
        return match (true) {
            $this->versionEqualTo('1.0.0') => $this->client->getPactSpecificationV1(),
            $this->versionEqualTo('1.1.0') => $this->client->getPactSpecificationV1_1(),
            $this->versionEqualTo('2.0.0') => $this->client->getPactSpecificationV2(),
            $this->versionEqualTo('3.0.0') => $this->client->getPactSpecificationV3(),
            $this->versionEqualTo('4.0.0') => $this->client->getPactSpecificationV4(),
            default => call_user_func(function () {
                trigger_error(sprintf("Specification version '%s' is unknown", $this->config->getPactSpecificationVersion()), E_USER_WARNING);

                return $this->client->getPactSpecificationUnknown();
            }),
        };
    }

    protected function validatePact(): void
    {
        if (!$this->pact) {
            throw new MissingPactException();
        }
    }

    private function versionEqualTo(string $version): bool
    {
        return Comparator::equalTo($this->config->getPactSpecificationVersion(), $version);
    }

    private function initWithLogLevel(): void
    {
        $logLevel = $this->config->getLogLevel();
        if ($logLevel) {
            $this->client->call('pactffi_init_with_log_level', $logLevel);
        }
    }

    private function newPact(): void
    {
        $this->pact = new Pact($this->client->call('pactffi_new_pact', $this->config->getConsumer(), $this->config->getProvider()));
    }

    private function withSpecification(): void
    {
        $this->client->call('pactffi_with_specification', $this->pact->handle, $this->getSpecification());
    }
}
