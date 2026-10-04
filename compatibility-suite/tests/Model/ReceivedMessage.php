<?php

namespace PhpPactTest\CompatibilitySuite\Model;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * A message received by a V3/V4 message consumer, as reified by the
 * message consumer FFI (contents, metadata).
 */
final class ReceivedMessage
{
    /**
     * @param array<array-key, mixed> $data
     */
    private function __construct(private readonly array $data)
    {
    }

    public static function fromJson(string $json): ?self
    {
        $data = json_decode($json, true);

        return is_array($data) ? new self($data) : null;
    }

    /**
     * @return mixed the raw (decoded JSON) contents of the message
     */
    public function getContents(): mixed
    {
        return $this->data['contents'] ?? null;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getMetadata(): array
    {
        return (array) ($this->data['metadata'] ?? []);
    }

    public function getContentType(): string
    {
        return TypeCaster::toString($this->getMetadata()['contentType'] ?? '');
    }
}
