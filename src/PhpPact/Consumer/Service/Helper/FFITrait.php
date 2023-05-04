<?php

namespace PhpPact\Consumer\Service\Helper;

use FFI;
use PhpPact\Consumer\Exception\HeaderNotReadException;
use PhpPact\Standalone\Installer\Model\Scripts;

trait FFITrait
{
    private FFI $ffi;

    private function createFFI(): void
    {
        $code = \file_get_contents(Scripts::getHeader());
        if (!is_string($code)) {
            throw new HeaderNotReadException();
        }
        $this->ffi = FFI::cdef($code, Scripts::getLibrary());
    }
}
