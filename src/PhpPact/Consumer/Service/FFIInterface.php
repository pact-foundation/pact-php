<?php

namespace PhpPact\Consumer\Service;

interface FFIInterface
{
    /**
     * @param array<int, mixed> $arguments
     */
    public function call(string $name, ...$arguments): mixed;

    public function get(string $name): mixed;
}
