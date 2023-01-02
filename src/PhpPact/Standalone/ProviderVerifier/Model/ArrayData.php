<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use FFI\CData;
use FFI;

class ArrayData
{
    public function __construct(
        private ?CData $items = null,
        private int $size = 0
    ) {
    }

    public function getItems(): ?CData
    {
        return $this->items;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public static function createFrom(iterable $values): self
    {
        $size = count($values);
        if ($size === 0) {
            return new self();
        }

        $items = FFI::new("char*[{$size}]");
        foreach ($values as $index => $value) {
            $length = \strlen($value);
            $itemSize = $length + 1;
            $item  = FFI::new("char[{$itemSize}]", false);
            FFI::memcpy($item, $value, $length);
            $items[$index] = $item;
        }

        return new self($items, $size);
    }

    public function __destruct()
    {
        for ($i=0; $i < $this->size; $i++) {
            FFI::free($this->items[$i]);
        }
    }
}
