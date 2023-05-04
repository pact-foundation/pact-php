<?php

namespace PhpPact\Consumer\Service;

use Composer\Semver\Comparator;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Exception\PactFileNotWroteException;
use PhpPact\Consumer\Service\Helper\SpecificationTrait;

class PactRegistry implements PactRegistryInterface
{
    use SpecificationTrait;

    private int $pactId;

    public function __construct(private PactConfigInterface $config)
    {
        $this->createFFI();
        $this->initWithLogLevel();
    }

    public function registerPact(): void
    {
        $this
            ->newPact()
            ->withSpecification();
    }

    public function getId(): int
    {
        return $this->pactId;
    }

    public function cleanUp(): void
    {
        $this->ffi->pactffi_free_pact_handle($this->getId());
        unset($this->pactId);
    }

    public function writePact(): void
    {
        $error = $this->ffi->pactffi_pact_handle_write_file(
            $this->getId(),
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
        );
        if ($error) {
            throw new PactFileNotWroteException($error);
        }
    }

    private function initWithLogLevel(): self
    {
        $logLevel = $this->config->getLogLevel();
        if ($logLevel) {
            $this->ffi->pactffi_init_with_log_level($logLevel);
        }

        return $this;
    }

    private function newPact(): self
    {
        $this->pactId = $this->ffi->pactffi_new_pact($this->config->getConsumer(), $this->config->getProvider());

        return $this;
    }

    private function withSpecification(): self
    {
        $this->ffi->pactffi_with_specification($this->getId(), $this->getSpecification());

        return $this;
    }

    private function versionEqualTo(string $version): bool
    {
        return Comparator::equalTo($this->config->getPactSpecificationVersion(), $version);
    }
}
