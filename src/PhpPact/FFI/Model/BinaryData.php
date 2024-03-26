<?php

namespace PhpPact\FFI\Model;

use FFI\CData;
use FFI;
use PhpPact\FFI\Exception\CDataNotCreatedException;
use PhpPact\FFI\Exception\EmptyBinaryFileNotSupportedException;

class BinaryData
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

    public static function createFrom(string $contents): self
    {
        if (empty($contents)) {
            throw new EmptyBinaryFileNotSupportedException();
        }

        $length = \strlen($contents);
        $cData  = FFI::new("uint8_t[{$length}]");
        if ($cData === null) {
            throw new CDataNotCreatedException();
        }
        FFI::memcpy($cData, $contents, $length);

        return new self($cData, $length);
    }

    public function __toString(): string
    {
        $result = '';
        for ($index = 0; $index < $this->size; $index++) {
            $result .= chr($this->value[$index]); // @phpstan-ignore-line
        }

        return $result;
    }
}
