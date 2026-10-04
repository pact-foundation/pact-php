<?php

namespace PhpPactTest\CompatibilitySuite\Model\Pact;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * A Pact interaction (V1 to V4, HTTP, async message or synchronous message).
 */
final class Interaction
{
    public const TYPE_HTTP = 'HTTP';
    public const TYPE_ASYNC_MESSAGE = 'Asynchronous';
    public const TYPE_SYNC_MESSAGE = 'Synchronous';

    /**
     * @param array<array-key, mixed> $pact the whole Pact array, kept by reference so mutations are persisted
     */
    public function __construct(private array &$pact, private readonly int $index)
    {
    }

    public function getType(): string
    {
        return TypeCaster::toString($this->getAttribute('type') ?? '');
    }

    public function getDescription(): string
    {
        return TypeCaster::toString($this->getAttribute('description') ?? '');
    }

    public function getKey(): ?string
    {
        $key = $this->getAttribute('key');

        return null === $key ? null : TypeCaster::toString($key);
    }

    public function isPending(): bool
    {
        return (bool) ($this->getAttribute('pending') ?? false);
    }

    public function setPending(bool $pending): void
    {
        $this->setAttribute('pending', $pending);
    }

    /**
     * @param array<array-key, mixed> $comments
     */
    public function setComments(array $comments): void
    {
        $this->setAttribute('comments', $comments);
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

    /**
     * @param list<ProviderState|array<array-key, mixed>> $providerStates
     */
    public function setProviderStates(array $providerStates): void
    {
        $this->setAttribute('providerStates', array_values(array_map(
            fn (ProviderState|array $state): array => $state instanceof ProviderState ? $state->toArray() : $state,
            $providerStates
        )));
    }

    /**
     * @param array<array-key, mixed> $params
     */
    public function addProviderState(string $name, array $params = []): void
    {
        $providerStates = (array) ($this->getAttribute('providerStates') ?? []);
        $providerStates[] = [] === $params
            ? ['name' => $name]
            : ['name' => $name, 'params' => $params];
        $this->setAttribute('providerStates', $providerStates);
    }

    public function getRequest(): Request
    {
        return new Request((array) ($this->getAttribute('request') ?? []));
    }

    /**
     * In V4 Pact files the response is a list of responses; in V1/V3 it is a single object.
     */
    public function getResponse(int $index = 0): Response
    {
        $response = (array) ($this->getAttribute('response') ?? []);
        if (array_is_list($response)) {
            $response = $response[$index] ?? [];
        }

        return new Response((array) $response);
    }

    /**
     * @return mixed the raw (decoded JSON) value of the given attribute
     */
    public function getAttribute(string $name): mixed
    {
        $interactions = (array) ($this->pact['interactions'] ?? []);
        $interaction = (array) ($interactions[$this->index] ?? []);

        return $interaction[$name] ?? null;
    }

    private function setAttribute(string $name, mixed $value): void
    {
        $interactions = (array) ($this->pact['interactions'] ?? []);
        $interaction = (array) ($interactions[$this->index] ?? []);
        $interaction[$name] = $value;
        $interactions[$this->index] = $interaction;
        $this->pact['interactions'] = $interactions;
    }
}
