<?php

namespace PhpPactTest\Helper;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use ReflectionProperty;

trait FactoryTrait
{
    /**
     * @param array<string, string> $properties
     */
    private function assertPropertiesInstanceOf(object $object, ?string $class, array $properties): void
    {
        foreach ($properties as $property => $propertyClass) {
            $reflection = new ReflectionProperty($class ?? $object, $property);
            $value = $reflection->getValue($object);
            $this->assertInstanceOf($propertyClass, $value);
        }
    }

    private function cleanUp(object $driver): void
    {
        $reflection = new ReflectionProperty($driver, 'pactDriver');
        $pactDriver = $reflection->getValue($driver);
        $this->assertInstanceOf(PactDriverInterface::class, $pactDriver);
        $pactDriver->cleanUp();
    }
}
