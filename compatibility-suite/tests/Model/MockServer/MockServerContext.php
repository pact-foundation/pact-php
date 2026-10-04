<?php

namespace PhpPactTest\CompatibilitySuite\Model\MockServer;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * A mock server context, as provided in the "mockServer" context of
 * the generators feature (currently only carries the href to inject).
 */
final class MockServerContext
{
    /**
     * @param array<array-key, mixed> $data
     */
    private function __construct(private readonly array $data)
    {
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(is_array($data) ? $data : []);
    }

    public function getHref(): string
    {
        return TypeCaster::toString($this->data['href'] ?? '');
    }
}
