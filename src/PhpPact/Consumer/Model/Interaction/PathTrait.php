<?php

namespace PhpPact\Consumer\Model\Interaction;

use JsonException;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

trait PathTrait
{
    private string $path;

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @throws JsonException
     */
    public function setPath(MatcherInterface|string $path): self
    {
        $this->path = is_string($path) ? $path : json_encode($path, JSON_THROW_ON_ERROR);

        return $this;
    }
}
