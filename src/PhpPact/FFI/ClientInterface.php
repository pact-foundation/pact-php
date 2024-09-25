<?php

namespace PhpPact\FFI;

use PhpPact\FFI\Model\BinaryData;
use PhpPact\FFI\Model\Result;

interface ClientInterface
{
    public function withBinaryFile(int $interaction, int $part, string $contentType, BinaryData $data): bool;

    public function withBody(int $interaction, int $part, string $contentType, string $contents): bool;

    public function withMultipartFileV2(int $interaction, int $part, string $contentType, string $path, string $name, string $boundary): Result;

    public function getInteractionPartRequest(): int;

    public function getInteractionPartResponse(): int;

    public function getPactSpecificationV1(): int;

    public function getPactSpecificationV1_1(): int;

    public function getPactSpecificationV2(): int;

    public function getPactSpecificationV3(): int;

    public function getPactSpecificationV4(): int;

    public function getPactSpecificationUnknown(): int;

    /**
     * @param array<int, mixed> $arguments
     */
    public function call(string $name, ...$arguments): mixed;

    public function get(string $name): mixed;
}
