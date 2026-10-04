<?php

namespace PhpPactTest\CompatibilitySuite\Model\Pact;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * A V3 message pact message.
 */
final class Message
{
    /**
     * @param array<array-key, mixed> $pact the whole Pact array, kept by reference so mutations are persisted
     */
    public function __construct(private array &$pact, private readonly int $index)
    {
    }

    public function getDescription(): string
    {
        return TypeCaster::toString($this->getAttribute('description') ?? '');
    }

    /**
     * @return mixed the raw (decoded JSON) contents of the message
     */
    public function getContents(): mixed
    {
        return $this->getAttribute('contents');
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getMetadata(): array
    {
        return (array) ($this->getAttribute('metadata') ?? []);
    }

    /**
     * @return list<ProviderState>
     */
    public function getProviderStates(): array
    {
        return array_values(array_map(
            fn (mixed $state): ProviderState => new ProviderState((array) $state),
            (array) ($this->getAttribute('providerStates') ?? [])
        ));
    }

    public function setProviderState(string $state): void
    {
        $this->setAttribute('providerState', $state);
    }

    /**
     * @param array<array-key, mixed> $metaData
     */
    public function setMetaData(array $metaData): void
    {
        $this->setAttribute('metaData', $metaData);
    }

    /**
     * @param array<array-key, mixed> $metadata
     */
    public function mergeMetadata(array $metadata): void
    {
        $this->setAttribute('metadata', array_merge($this->getMetadata(), $metadata));
    }

    public function setMatchingRules(mixed $matchingRules): void
    {
        $this->setAttribute('matchingRules', $matchingRules);
    }

    /**
     * @return mixed the raw (decoded JSON) value of the given attribute
     */
    public function getAttribute(string $name): mixed
    {
        $messages = (array) ($this->pact['messages'] ?? []);
        $message = (array) ($messages[$this->index] ?? []);

        return $message[$name] ?? null;
    }

    private function setAttribute(string $name, mixed $value): void
    {
        $messages = (array) ($this->pact['messages'] ?? []);
        $message = (array) ($messages[$this->index] ?? []);
        $message[$name] = $value;
        $messages[$this->index] = $message;
        $this->pact['messages'] = $messages;
    }
}
