<?php

namespace PhpPact\Consumer\Registry\Interaction\Contents;

interface ContentsRegistryInterface
{
    public function withContents(?string $contentType = null, ?string $contents = null): void;
}
