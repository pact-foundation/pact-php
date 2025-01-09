<?php

namespace PhpPact\FFI;

use FFI\CData;
use PhpPact\FFI\Model\ArrayData;
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

    public function cleanupMockServer(int $port): bool;

    public function mockServerMatched(int $port): bool;

    public function mockServerMismatches(int $port): string;

    public function writePactFile(int $port, string $directory, bool $overwrite): int;

    public function createMockServerForTransport(int $pact, string $host, int $port, string $transport, ?string $transportConfig): int;

    public function verifierNewForApplication(?string $name, ?string $version): ?CData;

    public function verifierSetProviderInfo(CData $handle, ?string $name, ?string $scheme, ?string $host, ?int $port, ?string $path): void;

    public function verifierAddProviderTransport(CData $handle, ?string $protocol, ?int $port, ?string $path, ?string $scheme): void;

    public function verifierSetFilterInfo(CData $handle, ?string $filterDescription, ?string $filterState, bool $filterNoState): void;

    public function verifierSetProviderState(CData $handle, ?string $url, bool $teardown, bool $body): void;

    public function verifierSetVerificationOptions(CData $handle, bool $disableSslVerification, int $requestTimeout): int;

    public function verifierSetPublishOptions(CData $handle, string $providerVersion, ?string $buildUrl, ?ArrayData $providerTags, ?string $providerBranch): int;

    public function verifierSetConsumerFilters(CData $handle, ?ArrayData $consumerFilters): void;

    public function verifierAddCustomHeader(CData $handle, string $name, string $value): void;

    public function verifierAddFileSource(CData $handle, string $file): void;

    public function verifierAddDirectorySource(CData $handle, string $directory): void;

    public function verifierAddUrlSource(CData $handle, string $url, ?string $username, ?string $password, ?string $token): void;

    public function verifierBrokerSourceWithSelectors(
        CData $handle,
        string $url,
        ?string $username,
        ?string $password,
        ?string $token,
        bool $enablePending,
        ?string $includeWipPactsSince,
        ?ArrayData $providerTags,
        ?string $providerBranch,
        ?ArrayData $consumerVersionSelectors,
        ?ArrayData $consumerVersionTags
    ): void;

    public function verifierExecute(CData $handle): int;

    public function verifierJson(CData $handle): ?string;

    public function verifierShutdown(CData $handle): void;

    public function messageReify(int $message): string;

    public function withHeaderV2(int $interaction, int $part, string $name, int $index, ?string $value): bool;

    public function withQueryParameterV2(int $interaction, string $name, int $index, ?string $value): bool;

    public function withRequest(int $interaction, ?string $requestMethod, ?string $path): bool;

    public function responseStatusV2(int $interaction, ?string $status): bool;

    public function interactionContents(int $interaction, int $part, string $contentType, string $contents): int;

    public function loggerInit(): void;

    public function loggerAttachSink(string $sinkSpecifier, int $levelFilter): int;

    public function loggerApply(): int;

    public function fetchLogBuffer(?string $logId = null): string;

    public function getInteractionPartRequest(): int;

    public function getInteractionPartResponse(): int;

    public function getPactSpecificationV1(): int;

    public function getPactSpecificationV1_1(): int;

    public function getPactSpecificationV2(): int;

    public function getPactSpecificationV3(): int;

    public function getPactSpecificationV4(): int;

    public function getPactSpecificationUnknown(): int;

    public function getLevelFilterTrace(): int;

    public function getLevelFilterDebug(): int;

    public function getLevelFilterInfo(): int;

    public function getLevelFilterWarn(): int;

    public function getLevelFilterError(): int;

    public function getLevelFilterOff(): int;

    /**
     * @deprecated 10.1.0 This method is deprecated and will be removed in a future release.
     *                    Please use the more specific methods in this interface instead,
     *                    as they provide clearer intent and stricter type definitions.
     */
    public function call(string $name, mixed ...$arguments): mixed;

    /**
     * @deprecated 10.1.0 This method is deprecated and will be removed in a future release.
     *                    Please use the more specific methods in this interface instead,
     *                    as they provide clearer intent and stricter type definitions.
     */
    public function get(string $name): mixed;
}
