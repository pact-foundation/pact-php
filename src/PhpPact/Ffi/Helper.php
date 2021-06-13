<?php

namespace PhpPact\Ffi;

use FFI;

/**
 * Class Helper.
 */
class Helper
{
    /**
     * @param string $contents
     *
     * @return Data
     */
    public static function getString(string $contents): Data
    {
        $length = \strlen($contents);
        $size   = $length + 1;
        $cData  = FFI::new("uint8_t[{$size}]");
        FFI::memcpy($cData, $contents, $length);

        return new Data($cData, $size);
    }

    /**
     * @param iterable|string[] $items
     *
     * @return Data
     */
    public static function getArray(iterable $items): Data
    {
        $itemsSize = count($items);
        if ($itemsSize === 0) {
            return new Data();
        }

        $cDataItems  = FFI::new("char*[{$itemsSize}]");
        foreach ($items as $index => $item) {
            $length = \strlen($item);
            $itemSize   = $length + 1;
            $cDataItem  = FFI::new("char[{$itemSize}]", false);
            FFI::memcpy($cDataItem, $item, $length);
            $cDataItems[$index] = $cDataItem;
        }

        return new Data($cDataItems, $itemsSize);
    }
}
