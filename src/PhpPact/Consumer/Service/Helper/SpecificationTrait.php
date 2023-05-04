<?php

namespace PhpPact\Consumer\Service\Helper;

trait SpecificationTrait
{
    use FFITrait;

    private function getSpecification(): int
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
}
