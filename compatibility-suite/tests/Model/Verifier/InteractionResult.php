<?php

namespace PhpPactTest\CompatibilitySuite\Model\Verifier;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * The result of verifying a single interaction, as serialized by
 * VerificationExecutionResult (see pact-reference/rust/pact_verifier/src/verification_result.rs).
 */
final class InteractionResult
{
    public const RESULT_OK = 'OK';
    public const RESULT_ERROR = 'Error';

    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function getInteractionId(): ?string
    {
        return isset($this->data['interactionId']) ? TypeCaster::toString($this->data['interactionId']) : null;
    }

    public function getInteractionKey(): ?string
    {
        return isset($this->data['interactionKey']) ? TypeCaster::toString($this->data['interactionKey']) : null;
    }

    public function getConsumer(): string
    {
        return TypeCaster::toString($this->data['consumer'] ?? '');
    }

    public function getProvider(): string
    {
        return TypeCaster::toString($this->data['provider'] ?? '');
    }

    public function getDescription(): string
    {
        return TypeCaster::toString($this->data['description'] ?? '');
    }

    /**
     * @return list<string>
     */
    public function getProviderStates(): array
    {
        return array_values(array_map(
            fn (mixed $state): string => TypeCaster::toString($state),
            (array) ($this->data['providerStates'] ?? [])
        ));
    }

    public function isPending(): bool
    {
        return (bool) ($this->data['pending'] ?? false);
    }

    public function getResult(): string
    {
        return TypeCaster::toString($this->data['result'] ?? '');
    }

    public function getMismatch(): ?VerificationMismatch
    {
        if (!isset($this->data['mismatch'])) {
            return null;
        }

        return new VerificationMismatch((array) $this->data['mismatch']);
    }

    public function getDuration(): string
    {
        return TypeCaster::toString($this->data['duration'] ?? '');
    }
}
