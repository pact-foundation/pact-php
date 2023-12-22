<?php

namespace PhpPactTest\CompatibilitySuite\Service;

interface ProviderStateServerInterface
{
    public const ACTION_SETUP = 'setup';
    public const ACTION_TEARDOWN = 'teardown';

    public function start(): void;

    public function stop(): void;

    public function getPort(): int;

    public function hasAction(string $action): bool;

    public function hasState(string $action, string $state, array $params = []): bool;
}
