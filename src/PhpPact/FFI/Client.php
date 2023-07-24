<?php

namespace PhpPact\FFI;

use FFI;
use PhpPact\FFI\Exception\HeaderNotReadException;
use PhpPact\Standalone\Installer\Model\Scripts;

class Client implements ClientInterface
{
    private FFI $ffi;

    public function __construct()
    {
        $code = \file_get_contents(Scripts::getHeader());
        if (!is_string($code)) {
            throw new HeaderNotReadException();
        }
        $this->ffi = FFI::cdef($code, Scripts::getLibrary());
    }

    public function call(string $name, ...$arguments): mixed
    {
        return $this->ffi->{$name}(...$arguments);
    }

    public function get(string $name): mixed
    {
        return $this->ffi->{$name};
    }
}
