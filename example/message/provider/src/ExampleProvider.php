<?php

namespace MessageProvider;

class ExampleProvider
{
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

    private array $currentState = [];

    public function dispatchMessage(string $description, array $providerStates): ?ExampleMessage
    {
        if ($description !== 'an alligator named Mary exists') {
            return null;
        }

        return (new ExampleMessage($this->message['contents'], $this->message['metadata']));
    }

    public function changeSate(string $action, string $state, array $params): void
    {
        $this->currentState = [
            'action' => $action,
            'state' => $state,
            'params' => $params,
        ];
    }
}
