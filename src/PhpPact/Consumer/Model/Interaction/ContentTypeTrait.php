<?php

namespace PhpPact\Consumer\Model\Interaction;

trait ContentTypeTrait
{
    private ?string $contentType = null;

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(?string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }
}
