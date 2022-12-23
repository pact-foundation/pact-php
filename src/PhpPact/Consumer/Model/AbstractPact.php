<?php

namespace PhpPact\Consumer\Model;

use FFI;
use PhpPact\Standalone\Installer\Model\Scripts;

/**
 * Class AbstractPact.
 */
abstract class AbstractPact
{
    protected FFI $ffi;
    protected int $id;

    public function __construct()
    {
        $this->ffi = FFI::cdef(\file_get_contents(Scripts::getCode()), Scripts::getLibrary());
    }
}
