<?php

namespace PhpPact\Consumer\Model\Body;

trait ContentTypeTrait
{
    private string $contentType;

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }
}
