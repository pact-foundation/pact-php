<?php

namespace PhpPact\Ffi;

use FFI\CData;

class Data
{
    protected ?CData $value;
    protected ?int $size;

    public function __construct(?CData $value = null, ?int $size = null)
    {
        $this->value = $value;
        $this->size = $size;
    }

    public function getValue(): ?CData
    {
        return $this->value;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }
}
