<?php

namespace PhpPact\Consumer\Driver\Interaction\Contents;

interface ContentsDriverInterface
{
    public function withContents(?string $contentType = null, ?string $contents = null): void;
}
