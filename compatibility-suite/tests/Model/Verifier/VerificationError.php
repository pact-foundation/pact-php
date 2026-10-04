<?php

namespace PhpPactTest\CompatibilitySuite\Model\Verifier;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * A verification error entry (pendingErrors/errors item), as serialized by
 * VerificationExecutionResult (see pact-reference/rust/pact_verifier/src/verification_result.rs).
 */
final class VerificationError
{
    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function getInteraction(): string
    {
        return TypeCaster::toString($this->data['interaction'] ?? '');
    }

    public function getMismatch(): VerificationMismatch
    {
        return new VerificationMismatch((array) ($this->data['mismatch'] ?? []));
    }
}
