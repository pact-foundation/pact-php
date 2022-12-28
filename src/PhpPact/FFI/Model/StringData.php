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

    public static function createFrom(string $value): ?self
    {
        $length = \strlen($value);
        $size   = $length + 1;
        $cData  = FFI::new("uint8_t[{$size}]");
        FFI::memcpy($cData, $value, $length);

        return new self($cData, $size);
    }
}
