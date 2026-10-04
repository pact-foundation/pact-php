<?php

namespace PhpPactTest\CompatibilitySuite\Model\PactBroker;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * The summary of a Pact broker matrix query, as returned by
 * the /matrix.json endpoint.
 */
final class MatrixSummary
{
    public const STATUS_UNKNOWN = 'unknown';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function getUnknown(): int
    {
        return TypeCaster::toInt($this->data[self::STATUS_UNKNOWN] ?? 0);
    }

    public function getSuccess(): int
    {
        return TypeCaster::toInt($this->data[self::STATUS_SUCCESS] ?? 0);
    }

    public function getFailed(): int
    {
        return TypeCaster::toInt($this->data[self::STATUS_FAILED] ?? 0);
    }
}
