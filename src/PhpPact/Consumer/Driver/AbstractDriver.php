<?php

namespace PhpPact\Consumer\Driver;

use Composer\Semver\Comparator;
use FFI;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Exception\PactFileNotWroteException;
use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\Consumer\Model\ProviderState;

abstract class AbstractDriver implements DriverInterface
{
    protected FFI $ffi;
    protected int $pactId;
    protected int $interactionId;

    public function __construct(protected PactConfigInterface $config)
    {
        $this->ffi = FFI::cdef(\file_get_contents(Scripts::getHeader()), Scripts::getLibrary());
        $this
            ->initWithLogLevel()
            ->newPact()
            ->withSpecification();
    }

    private function newPact(): self
    {
        $this->pactId = $this->ffi->pactffi_new_pact($this->config->getConsumer(), $this->config->getProvider());

        return $this;
    }

    protected function newInteraction(string $description): self
    {
        $this->interactionId = $this->ffi->pactffi_new_interaction($this->pactId, $description);

        return $this;
    }

    private function withSpecification(): self
    {
        $this->ffi->pactffi_with_specification($this->pactId, $this->getSpecification());

        return $this;
    }

    protected function getSpecification(): int
    {
        return match (true) {
            $this->versionEqualTo('1.0.0') => $this->ffi->PactSpecification_V1,
            $this->versionEqualTo('1.1.0') => $this->ffi->PactSpecification_V1_1,
            $this->versionEqualTo('2.0.0') => $this->ffi->PactSpecification_V2,
            $this->versionEqualTo('3.0.0') => $this->ffi->PactSpecification_V3,
            $this->versionEqualTo('4.0.0') => $this->ffi->PactSpecification_V4,
            default => function () {
                trigger_error(sprintf("Specification version '%s' is unknown", $this->config->getPactSpecificationVersion()), E_USER_WARNING);

                return $this->ffi->PactSpecification_Unknown;
            },
        };
    }

    protected function cleanUp(): void
    {
        $this->ffi->pactffi_free_pact_handle($this->pactId);
    }

    protected function writePact(): void
    {
        $error = $this->ffi->pactffi_pact_handle_write_file(
            $this->pactId,
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
        );
        if ($error) {
            throw new PactFileNotWroteException($error);
        }
    }

    protected function withBody(int $part, ?string $contentType = null, ?string $body = null): void
    {
        if (is_null($body)) {
            return;
        }
        $success = $this->ffi->pactffi_with_body($this->interactionId, $part, $contentType, $body);
        if (!$success) {
            throw new InteractionBodyNotAddedException();
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

    /**
     * @var array<ProviderState> $providerStates
     */
    protected function setProviderStates(array $providerStates): void
    {
        foreach ($providerStates as $providerState) {
            $this->ffi->pactffi_given($this->interactionId, $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->ffi->pactffi_given_with_param($this->interactionId, $providerState->getName(), $key, $value);
            }
        }
    }

    protected function setDescription(string $description): void
    {
        $this->ffi->pactffi_upon_receiving($this->interactionId, $description);
    }

    private function versionEqualTo(string $version): bool
    {
        return Comparator::equalTo($this->config->getPactSpecificationVersion(), $version);
    }
}
