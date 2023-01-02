<?php

namespace Provider;

use MessageProvider\ExampleMessageProvider;

class ExampleProvider
{
    /**
     * @var array
     */
    private array $messages;

    /**
     * @var array
     */
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

    /**
     * @param string $name
     *
     * @return string
     */
    public function sayHello(string $name): string
    {
        return "Hello, {$name}";
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function sayGoodbye(string $name): string
    {
        return "Goodbye, {$name}";
    }

    /**
     * @param string $description
     * @param array  $providerStates
     *
     * @return ExampleMessageProvider|null
     */
    public function dispatchMessage(string $description, array $providerStates): ?ExampleMessageProvider
    {
        if (!isset($this->messages[$description])) {
            return null;
        }

        return (new ExampleMessageProvider())
            ->setMetadata($this->messages[$description]['metadata'])
            ->setContents($this->messages[$description]['contents']);
    }

    /**
     * @param string $action
     * @param string $state
     * @param array  $params
     *
     * @return ExampleMessageProvider
     */
    public function changeSate(string $action, string $state, array $params): void
    {
        $this->currentState = [
            'action' => $action,
            'state' => $state,
            'params' => $params,
        ];
    }
}
