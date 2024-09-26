<?php

namespace PhpPact\FFI;

use FFI;
use FFI\CData;
use PhpPact\FFI\Exception\HeaderNotReadException;
use PhpPact\FFI\Exception\InvalidEnumException;
use PhpPact\FFI\Exception\InvalidResultException;
use PhpPact\FFI\Model\BinaryData;
use PhpPact\FFI\Model\Result;
use PhpPact\Standalone\Installer\Model\Scripts;

class Client implements ClientInterface
{
    private FFI $ffi;

    public function __construct()
    {
        $headerFile = Scripts::getHeader();
        $code = \file_get_contents($headerFile);
        if (!is_string($code)) {
            throw new HeaderNotReadException(sprintf('Can not read header file "%s"', $headerFile));
        }
        $this->ffi = FFI::cdef($code, Scripts::getLibrary());
    }

    public function withBinaryFile(int $interaction, int $part, string $contentType, BinaryData $data): bool
    {
        $method = 'pactffi_with_binary_file';
        $result = $this->call($method, $interaction, $part, $contentType, $data->getValue(), $data->getSize());
        if (!is_bool($result)) {
            throw new InvalidResultException(sprintf('Invalid result of "%s". Expected "boolean", but got "%s"', $method, get_debug_type($result)));
        }
        return $result;
    }

    public function withBody(int $interaction, int $part, string $contentType, string $contents): bool
    {
        $method = 'pactffi_with_body';
        $result = $this->call($method, $interaction, $part, $contentType, $contents);
        if (!is_bool($result)) {
            throw new InvalidResultException(sprintf('Invalid result of "%s". Expected "boolean", but got "%s"', $method, get_debug_type($result)));
        }
        return $result;
    }

    public function withMultipartFileV2(int $interaction, int $part, string $contentType, string $path, string $name, string $boundary): Result
    {
        $method = 'pactffi_with_multipart_file_v2';
        $result = $this->call($method, $interaction, $part, $contentType, $path, $name, $boundary);
        if (!$result instanceof CData) {
            throw new InvalidResultException(sprintf('Invalid result of "%s". Expected "%s", but got "%s"', $method, CData::class, get_debug_type($result)));
        }
        if ($result->tag === $this->getEnum('StringResult_Ok')) { // @phpstan-ignore-line
            return new Result(true, $result->ok instanceof CData ? FFI::string($result->ok) : ''); // @phpstan-ignore-line
        }
        if ($result->tag === $this->getEnum('StringResult_Failed')) { // @phpstan-ignore-line
            return new Result(false, $result->failed instanceof CData ? FFI::string($result->failed) : ''); // @phpstan-ignore-line
        }
        throw new InvalidResultException(sprintf('Invalid result of "%s". Neither ok or failed', $method));
    }

    public function setKey(int $interaction, string $key): bool
    {
        $method = 'pactffi_set_key';
        $result = $this->call($method, $interaction, $key);
        if (!is_bool($result)) {
            throw new InvalidResultException(sprintf('Invalid result of "%s". Expected "boolean", but got "%s"', $method, get_debug_type($result)));
        }
        return $result;
    }

    public function setPending(int $interaction, bool $pending): bool
    {
        $method = 'pactffi_set_pending';
        $result = $this->call($method, $interaction, $pending);
        if (!is_bool($result)) {
            throw new InvalidResultException(sprintf('Invalid result of "%s". Expected "boolean", but got "%s"', $method, get_debug_type($result)));
        }
        return $result;
    }

    public function setComment(int $interaction, string $key, ?string $value): bool
    {
        $method = 'pactffi_set_comment';
        $result = $this->call($method, $interaction, $key, $value);
        if (!is_bool($result)) {
            throw new InvalidResultException(sprintf('Invalid result of "%s". Expected "boolean", but got "%s"', $method, get_debug_type($result)));
        }
        return $result;
    }

    public function addTextComment(int $interaction, string $value): bool
    {
        $method = 'pactffi_add_text_comment';
        $result = $this->call($method, $interaction, $value);
        if (!is_bool($result)) {
            throw new InvalidResultException(sprintf('Invalid result of "%s". Expected "boolean", but got "%s"', $method, get_debug_type($result)));
        }
        return $result;
    }

    public function getInteractionPartRequest(): int
    {
        return $this->getEnum('InteractionPart_Request');
    }

    public function getInteractionPartResponse(): int
    {
        return $this->getEnum('InteractionPart_Response');
    }

    public function getPactSpecificationV1(): int
    {
        return $this->getEnum('PactSpecification_V1');
    }

    public function getPactSpecificationV1_1(): int
    {
        return $this->getEnum('PactSpecification_V1_1');
    }

    public function getPactSpecificationV2(): int
    {
        return $this->getEnum('PactSpecification_V2');
    }

    public function getPactSpecificationV3(): int
    {
        return $this->getEnum('PactSpecification_V3');
    }

    public function getPactSpecificationV4(): int
    {
        return $this->getEnum('PactSpecification_V4');
    }

    public function getPactSpecificationUnknown(): int
    {
        return $this->getEnum('PactSpecification_Unknown');
    }

    private function getEnum(string $name): int
    {
        $value = $this->get($name);
        if (!is_int($value)) {
            throw new InvalidEnumException(sprintf('Invalid enum "%s". Expected "int", but got "%s"', $name, get_debug_type($value)));
        }
        return $value;
    }

    public function call(string $name, ...$arguments): mixed
    {
        return $this->ffi->{$name}(...$arguments);
    }

    private function get(string $name): mixed
    {
        return $this->ffi->{$name};
    }
}
