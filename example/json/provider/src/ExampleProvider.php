<?php

namespace JsonProvider;

class ExampleProvider
{
    /**
     * @var array{action?: string, state?: string, params?: array<array-key, mixed>}
     */
    private array $currentState = [];

    public function sayHello(string $name): string
    {
        return "Hello, {$name}";
    }

    public function sayGoodbye(string $name): string
    {
        return "Goodbye, {$name}";
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
