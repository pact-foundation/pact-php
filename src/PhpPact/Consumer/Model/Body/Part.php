<?php

namespace PhpPact\Consumer\Model\Body;

class Part
{
    use ContentTypeTrait;

    public function __construct(private string $path, private string $name, string $contentType)
    {
        $this->setContentType($contentType);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
