<?php

namespace MessageProvider;

class ExampleProvider
{
    /**
     * @var array{
     *     metadata: array<string, mixed>,
     *     contents: array<string, mixed>
     * }
     */
    private array $message = [
        'metadata' => [
            'queue'       => 'wind cries',
            'routing_key' => 'wind cries',
        ],
        'contents' => [
            'text'   => 'Hello Mary',
            'number' => 123,
        ]
    ];

    /**
     * @var array{action?: string, state?: string, params?: array<array-key, mixed>}
     */
    private array $currentState = [];

    /**
     * @param array<array-key, mixed> $providerStates
     */
    public function dispatchMessage(string $description, array $providerStates): ?ExampleMessage
    {
        if ($description !== 'an alligator named Mary exists') {
            return null;
        }

        return (new ExampleMessage($this->message['contents'], $this->message['metadata']));
    }

    /**
     * @param array<array-key, mixed> $params
     */
    public function changeState(string $action, string $state, array $params): void
    {
        $this->currentState = [
            'action' => $action,
            'state' => $state,
            'params' => $params,
        ];
    }

    /**
     * @return array{action?: string, state?: string, params?: array<array-key, mixed>}
     */
    public function getCurrentState(): array
    {
        return $this->currentState;
    }
}
