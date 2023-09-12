<?php

namespace PhpPact\FFI\Model;

use FFI\CData;
use FFI;

class StringData
{
    private function __construct(
        private CData $value,
        private int $size
    ) {
    }

    public function getValue(): CData
    {
        return $this->value;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public static function createFrom(string $value, bool $nullTerminated = true): self
    {
        $length = \strlen($value);
        $size   = $length + ($nullTerminated ? 1 : 0);
        $cData  = FFI::new("uint8_t[{$size}]");
        FFI::memcpy($cData, $value, $length);

        return new self($cData, $size);
    }
}
