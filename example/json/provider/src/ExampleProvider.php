<?php

namespace JsonProvider;

class ExampleProvider
{
    private array $currentState = [];

    public function sayHello(string $name): string
    {
        return "Hello, {$name}";
    }

    public function sayGoodbye(string $name): string
    {
        return "Goodbye, {$name}";
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
