<?php

namespace PhpPactTest\CompatibilitySuite\Model\Verifier;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * The JSON output of a provider verification run, as serialized by
 * VerificationExecutionResult (see pact-reference/rust/pact_verifier/src/verification_result.rs).
 */
final class VerifierOutput
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

    public function isSuccessful(): bool
    {
        return (bool) ($this->data['result'] ?? false);
    }

    /**
     * @return list<array<array-key, mixed>>
     */
    public function getNotices(): array
    {
        return array_values(array_map(fn (mixed $notice): array => (array) $notice, (array) ($this->data['notices'] ?? [])));
    }

    /**
     * @return list<string>
     */
    public function getOutput(): array
    {
        return array_values(array_map(fn (mixed $line): string => TypeCaster::toString($line), (array) ($this->data['output'] ?? [])));
    }

    /**
     * @return list<VerificationError>
     */
    public function getErrors(): array
    {
        return array_values(array_map(
            fn (mixed $error): VerificationError => new VerificationError((array) $error),
            (array) ($this->data['errors'] ?? [])
        ));
    }

    /**
     * @return list<VerificationError>
     */
    public function getPendingErrors(): array
    {
        return array_values(array_map(
            fn (mixed $error): VerificationError => new VerificationError((array) $error),
            (array) ($this->data['pendingErrors'] ?? [])
        ));
    }

    /**
     * @return list<InteractionResult>
     */
    public function getInteractionResults(): array
    {
        return array_values(array_map(
            fn (mixed $result): InteractionResult => new InteractionResult((array) $result),
            (array) ($this->data['interactionResults'] ?? [])
        ));
    }
}
