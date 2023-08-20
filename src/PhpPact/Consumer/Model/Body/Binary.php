<?php

namespace PhpPact\Consumer\Model\Body;

use PhpPact\FFI\Model\StringData;

class Binary
{
    use ContentTypeTrait;

    private StringData $contents;

    public function __construct(string $contents, string $contentType)
    {
        $this->setContents(StringData::createFrom($contents, false));
        $this->setContentType($contentType);
    }

    public function getContents(): StringData
    {
        return $this->contents;
    }

    public function setContents(StringData $contents): self
    {
        $this->contents = $contents;

        return $this;
    }
}
