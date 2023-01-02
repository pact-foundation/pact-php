<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use FFI\CData;
use FFI;

class ArrayData
{
    private function __construct(
        private CData $items,
        private int $size
    ) {
    }

    public function getItems(): CData
    {
        return $this->items;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param iterable<string> $values
     */
    public static function createFrom(iterable $values): ?self
    {
        $size = count($values);
        if ($size === 0) {
            return null;
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
