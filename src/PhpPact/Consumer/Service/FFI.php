<?php

namespace PhpPact\Consumer\Service;

use FFI as CoreFFI;
use PhpPact\Consumer\Exception\HeaderNotReadException;
use PhpPact\Standalone\Installer\Model\Scripts;

class FFI implements FFIInterface
{
    private CoreFFI $ffi;

    public function __construct()
    {
        $code = \file_get_contents(Scripts::getHeader());
        if (!is_string($code)) {
            throw new HeaderNotReadException();
        }
        $this->ffi = CoreFFI::cdef($code, Scripts::getLibrary());
    }

    /**
     * {@inheritdoc}
     */
    public function call(string $name, ...$arguments): mixed
    {
        return $this->ffi->{$name}(...$arguments);
    }

    public function get(string $name): mixed
    {
        return $this->ffi->{$name};
    }
}
