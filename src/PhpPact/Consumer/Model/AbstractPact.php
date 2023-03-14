<?php

namespace PhpPact\Consumer\Model;

use FFI;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\PactConfigInterface;
use PhpPact\Consumer\Exception\PactFileNotWroteException;
use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;

/**
 * Class AbstractPact.
 */
abstract class AbstractPact
{
    protected FFI $ffi;
    protected int $id;

    public function __construct(protected PactConfigInterface $config)
    {
        $this
            ->createFfi()
            ->newPact()
            ->withSpecification();
    }

    private function createFfi(): self
    {
        $this->ffi = FFI::cdef(\file_get_contents(Scripts::getHeader()), Scripts::getLibrary());

        return $this;
    }

    private function newPact(): self
    {
        $this->id = $this->ffi->pactffi_new_pact($this->config->getConsumer(), $this->config->getProvider());

        return $this;
    }

    protected function getSpecification(): int
    {
        $supportedVersions = [
            '1.0.0' => $this->ffi->PactSpecification_V1,
            '1.1.0' => $this->ffi->PactSpecification_V1_1,
            '2.0.0' => $this->ffi->PactSpecification_V2,
            '3.0.0' => $this->ffi->PactSpecification_V3,
            '4.0.0' => $this->ffi->PactSpecification_V4,
        ];
        $version = $this->config->getPactSpecificationVersion();
        if (isset($supportedVersions[$version])) {
            $specification = $supportedVersions[$version];
        } else {
            trigger_error(sprintf("Specification version '%s' is unknown", $version), E_USER_WARNING);
            $specification = $this->ffi->PactSpecification_Unknown;
        }

        return $specification;
    }

    private function withSpecification(): self
    {
        $this->ffi->pactffi_with_specification($this->id, $this->getSpecification());

        return $this;
    }

    protected function newInteraction(?string $description): int
    {
        return $this->ffi->pactffi_new_interaction($this->id, $description);
    }

    protected function cleanUp(): void
    {
        $this->ffi->pactffi_free_pact_handle($this->id);
    }

    protected function writePact(): void
    {
        $error = $this->ffi->pactffi_pact_handle_write_file(
            $this->id,
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
        );
        if ($error) {
            throw new PactFileNotWroteException($error);
        }
    }

    protected function withBody(int $interaction, int $part, ?string $contentType = null, ?string $body = null): void
    {
        if (is_null($body)) {
            return;
        }
        $success = $this->ffi->pactffi_with_body($interaction, $part, $contentType, $body);
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }
}
