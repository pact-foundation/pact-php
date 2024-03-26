<?php

namespace PhpPact\Consumer\Model\Body;

use PhpPact\Consumer\Exception\BinaryFileNotExistException;
use PhpPact\Consumer\Exception\BinaryFileReadException;
use PhpPact\FFI\Model\BinaryData;

class Binary
{
    use ContentTypeTrait;

    private ?BinaryData $data = null;

    public function __construct(private string $path, string $contentType)
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

    public function getData(): BinaryData
    {
        if (!$this->data) {
            $this->data = $this->createBinaryData();
        }

        return $this->data;
    }

    private function createBinaryData(): BinaryData
    {
        if (!file_exists($this->getPath())) {
            throw new BinaryFileNotExistException(sprintf('File %s does not exist', $this->getPath()));
        }
        $contents = file_get_contents($this->getPath());
        if (false === $contents) {
            throw new BinaryFileReadException(sprintf('File %s can not be read', $this->getPath()));
        }

        return BinaryData::createFrom($contents);
    }

    public function __destruct()
    {
        $this->data = null;
    }
}
