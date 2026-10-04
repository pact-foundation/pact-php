<?php

namespace PhpPactTest\CompatibilitySuite\Model\Verifier;

use PhpPactTest\CompatibilitySuite\Model\Mismatch\Mismatch;
use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * The mismatch of a verification error, as serialized by
 * VerificationMismatchResult (see pact-reference/rust/pact_verifier/src/verification_result.rs).
 */
final class VerificationMismatch
{
    public const TYPE_ERROR = 'error';
    public const TYPE_MISMATCHES = 'mismatches';

    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function getType(): string
    {
        return TypeCaster::toString($this->data['type'] ?? '');
    }

    public function getMessage(): string
    {
        return TypeCaster::toString($this->data['message'] ?? '');
    }

    public function getInteractionId(): string
    {
        return TypeCaster::toString($this->data['interactionId'] ?? '');
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
