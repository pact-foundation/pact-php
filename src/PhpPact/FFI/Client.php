<?php

namespace PhpPact\FFI;

use FFI;
use FFI\CData;
use PhpPact\FFI\Exception\HeaderNotReadException;
use PhpPact\FFI\Exception\InvalidEnumException;
use PhpPact\FFI\Exception\InvalidResultException;
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

    public function getInteractionPartRequest(): int
    {
        return $this->getEnum('InteractionPart_Request');
    }

    public function getInteractionPartResponse(): int
    {
        return $this->getEnum('InteractionPart_Response');
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

    public function get(string $name): mixed
    {
        return $this->ffi->{$name};
    }
}
