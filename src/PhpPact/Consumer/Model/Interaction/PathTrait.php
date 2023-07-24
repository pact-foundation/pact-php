<?php

namespace PhpPact\Consumer\Model\Interaction;

use JsonException;

trait PathTrait
{
    private string $path;

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string|array<string, mixed> $path
     *
     * @throws JsonException
     */
    public function setPath(array|string $path): self
    {
        $this->path = is_array($path) ? json_encode($path, JSON_THROW_ON_ERROR) : $path;

        return $this;
    }
}
