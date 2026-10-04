<?php

namespace PhpPactTest\CompatibilitySuite\Model\MockServer;

use PhpPactTest\CompatibilitySuite\Model\Mismatch\Mismatch;
use PhpPactTest\CompatibilitySuite\Model\Pact\Request;
use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * A mock server mismatch entry, as returned by the mock server verify
 * (type request-mismatch, missing-request or request-not-found).
 */
final class RequestMismatch
{
    public const TYPE_REQUEST_MISMATCH = 'request-mismatch';
    public const TYPE_MISSING_REQUEST = 'missing-request';
    public const TYPE_REQUEST_NOT_FOUND = 'request-not-found';

    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    /**
     * @param array<array-key, mixed> $data
     * @return list<self>
     */
    public static function listFromArray(array $data): array
    {
        return array_values(array_map(
            fn (mixed $item): self => new self((array) $item),
            $data
        ));
    }

    public function getType(): string
    {
        return TypeCaster::toString($this->data['type'] ?? '');
    }

    public function getMethod(): string
    {
        return TypeCaster::toString($this->data['method'] ?? '');
    }

    public function getPath(): string
    {
        return TypeCaster::toString($this->data['path'] ?? '');
    }

    public function getRequest(): Request
    {
        return new Request((array) ($this->data['request'] ?? []));
    }

    /**
     * @return list<Mismatch>
     */
    public function getMismatches(): array
    {
        return array_values(array_map(
            fn (mixed $mismatch): Mismatch => new Mismatch((array) $mismatch),
            (array) ($this->data['mismatches'] ?? [])
        ));
    }
}
