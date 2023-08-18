<?php

namespace MessageProvider;

use MessageProvider\ExampleMessageProvider;

class ExampleProvider
{
    private array $messages;

    private array $currentState = [];

    public function __construct()
    {
        $this->messages = [
            'an alligator named Mary exists' => [
                'metadata' => [
                    'queue'       => 'wind cries',
                    'routing_key' => 'wind cries',
                ],
                'contents' => [
                    'text' => 'Hello Mary',
                ]
            ],
            'footprints dressed in red' => [
                'metadata' => [
                    'queue'       => 'And the clowns have all gone to bed',
                    'routing_key' => 'And the clowns have all gone to bed',
                ],
                'contents' => [
                    'song' => 'And the wind whispers Mary',
                ]
            ],
        ];
    }

    public function dispatchMessage(string $description, array $providerStates): ?ExampleMessageProvider
    {
        if (!isset($this->messages[$description])) {
            return null;
        }

        return (new ExampleMessageProvider())
            ->setMetadata($this->messages[$description]['metadata'])
            ->setContents($this->messages[$description]['contents']);
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
