<?php

namespace PhpPact\FFI\Model;

class Result
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message
    ) {
    }
}
