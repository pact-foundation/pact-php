<?php

namespace PhpPact\Consumer\Model\Body;

class Multipart
{
    /**
     * @param array<Part> $parts
     */
    public function __construct(private array $parts, private string $boundary)
    {
        $this->setParts($parts);
    }

    /**
     * @return array<Part>
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @param array<Part> $parts
     */
    public function setParts(array $parts): self
    {
        $this->parts = [];
        foreach ($parts as $part) {
            $this->addPart($part);
        }

        return $this;
    }

    public function addPart(Part $part): self
    {
        $this->parts[] = $part;

        return $this;
    }

    public function getBoundary(): string
    {
        return $this->boundary;
    }
}
