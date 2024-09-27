<?php

namespace PhpPact\FFI;

use PhpPact\FFI\Model\BinaryData;
use PhpPact\FFI\Model\Result;

interface ClientInterface
{
    public function withBinaryFile(int $interaction, int $part, string $contentType, BinaryData $data): bool;

    public function withBody(int $interaction, int $part, string $contentType, string $contents): bool;

    public function withMultipartFileV2(int $interaction, int $part, string $contentType, string $path, string $name, string $boundary): Result;

    public function setKey(int $interaction, string $key): bool;

    public function setPending(int $interaction, bool $pending): bool;

    public function setComment(int $interaction, string $key, ?string $value): bool;

    public function addTextComment(int $interaction, string $value): bool;

    public function newInteraction(int $pact, string $description): int;

    public function newMessageInteraction(int $pact, string $description): int;

    public function newSyncMessageInteraction(int $pact, string $description): int;

    public function given(int $interaction, string $name): bool;

    public function givenWithParam(int $interaction, string $name, string $key, string $value): bool;

    public function uponReceiving(int $interaction, string $description): bool;

    public function messageExpectsToReceive(int $message, string $description): void;

    public function messageWithMetadataV2(int $message, string $key, string $value): void;

    public function messageGiven(int $message, string $name): void;

    public function messageGivenWithParam(int $message, string $name, string $key, string $value): void;

    public function freePactHandle(int $pact): int;

    public function newPact(string $consumer, string $provider): int;

    public function withSpecification(int $pact, int $specification): bool;

    public function initWithLogLevel(string $logLevel): void;

    public function pactHandleWriteFile(int $pact, string $directory, bool $overwrite): int;

    public function cleanupPlugins(int $pact): void;

    public function usingPlugin(int $pact, string $name, ?string $version): int;

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
}
