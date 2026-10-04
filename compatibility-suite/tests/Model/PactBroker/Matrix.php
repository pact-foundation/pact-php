<?php

namespace PhpPactTest\CompatibilitySuite\Model\PactBroker;

/**
 * The result of a Pact broker matrix query, as returned by
 * the /matrix.json endpoint.
 */
final class Matrix
{
    /**
     * @param array<array-key, mixed> $data
     */
    private function __construct(private readonly array $data)
    {
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(is_array($data) ? $data : []);
    }

    public function getSummary(): MatrixSummary
    {
        return new MatrixSummary((array) ($this->data['summary'] ?? []));
    }
}
