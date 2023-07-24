<?php

namespace PhpPact\FFI\Model;

use FFI\CData;
use FFI;
use PhpPact\FFI\Exception\CDataNotCreatedException;

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
     * @param array<int, string> $values
     */
    public static function createFrom(array $values): ?self
    {
        $size = count($values);
        if ($size === 0) {
            return null;
        }

        $items = FFI::new("char*[{$size}]");
        if ($items === null) {
            return throw new CDataNotCreatedException();
        }
        foreach ($values as $index => $value) {
            $length = \strlen($value);
            $itemSize = $length + 1;
            $item = FFI::new("char[{$itemSize}]", false);
            if ($item === null) {
                return throw new CDataNotCreatedException();
            }
            FFI::memcpy($item, $value, $length);
            $items[$index] = $item; // @phpstan-ignore-line
        }

        return new self($items, $size);
    }

    public function __destruct()
    {
        for ($i=0; $i < $this->size; $i++) {
            FFI::free($this->items[$i]); // @phpstan-ignore-line
        }
    }
}
