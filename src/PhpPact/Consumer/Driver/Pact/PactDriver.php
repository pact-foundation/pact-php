<?php

namespace PhpPact\Consumer\Driver\Pact;

use Composer\Semver\Comparator;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Exception\PactFileNotWroteException;
use PhpPact\FFI\ProxyInterface;

class PactDriver implements PactDriverInterface
{
    protected int $id;

    public function __construct(
        private ProxyInterface $proxy,
        private PactConfigInterface $config
    ) {
        $this->setUp();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function cleanUp(): void
    {
        $this->proxy->call('pactffi_free_pact_handle', $this->id);
        unset($this->id);
    }

    public function writePact(): void
    {
        $error = $this->proxy->call(
            'pactffi_pact_handle_write_file',
            $this->id,
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
        );
        if ($error) {
            throw new PactFileNotWroteException($error);
        }
    }

    protected function setUp(): void
    {
        $this
            ->initWithLogLevel()
            ->newPact()
            ->withSpecification();
    }

    protected function getSpecification(): int
    {
        return match (true) {
            $this->versionEqualTo('1.0.0') => $this->proxy->get('PactSpecification_V1'),
            $this->versionEqualTo('1.1.0') => $this->proxy->get('PactSpecification_V1_1'),
            $this->versionEqualTo('2.0.0') => $this->proxy->get('PactSpecification_V2'),
            $this->versionEqualTo('3.0.0') => $this->proxy->get('PactSpecification_V3'),
            $this->versionEqualTo('4.0.0') => $this->proxy->get('PactSpecification_V4'),
            default => function () {
                trigger_error(sprintf("Specification version '%s' is unknown", $this->config->getPactSpecificationVersion()), E_USER_WARNING);

                return $this->proxy->get('PactSpecification_Unknown');
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
            $this->proxy->call('pactffi_init_with_log_level', $logLevel);
        }

        return $this;
    }

    private function newPact(): self
    {
        $this->id = $this->proxy->call('pactffi_new_pact', $this->config->getConsumer(), $this->config->getProvider());

        return $this;
    }

    private function withSpecification(): self
    {
        $this->proxy->call('pactffi_with_specification', $this->id, $this->getSpecification());

        return $this;
    }
}
