<?php

namespace PhpPact\FFI;

use PhpPact\FFI\Model\Result;

interface ClientInterface
{
    public function withMultipartFileV2(int $interaction, int $part, string $contentType, string $path, string $name, string $boundary): Result;

    public function getInteractionPartRequest(): int;

    public function getInteractionPartResponse(): int;

    /**
     * @param array<int, mixed> $arguments
     */
    public function call(string $name, ...$arguments): mixed;

    public function get(string $name): mixed;
}
