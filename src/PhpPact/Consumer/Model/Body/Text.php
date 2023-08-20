<?php

namespace PhpPact\Consumer\Model\Body;

class Text
{
    use ContentTypeTrait;

    private string $contents;

    public function __construct(string $contents, string $contentType)
    {
        $this->setContents($contents);
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
