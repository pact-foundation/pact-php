<?php

namespace PhpPact\Consumer\Model\Body;

class Text
{
    use ContentTypeTrait;

    public function __construct(private string $contents, string $contentType)
    {
        $this->setContentType($contentType);
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function setContents(string $contents): self
    {
        $this->contents = $contents;

        return $this;
    }
}
