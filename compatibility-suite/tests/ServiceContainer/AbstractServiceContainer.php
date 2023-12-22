<?php

namespace PhpPactTest\CompatibilitySuite\ServiceContainer;

use Behat\Behat\HelperContainer\Exception\ServiceNotFoundException;
use Psr\Container\ContainerInterface;

abstract class AbstractServiceContainer implements ContainerInterface
{
    private array $services = [];

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new ServiceNotFoundException(
                sprintf('Service `%s` not found.', $id),
                $id
            );
        }

        return $this->services[$id];
    }

    protected function set(string $id, mixed $service): void
    {
        $this->services[$id] = $service;
    }

    abstract protected function getSpecification(): string;
}
