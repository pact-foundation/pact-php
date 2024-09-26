<?php

namespace PhpPactTest\Helper;

use ReflectionProperty;

trait FactoryTrait
{
    /**
     * @param array<string, class-string<object>> $properties
     */
    private function assertPropertiesInstanceOf(object $object, ?string $class, array $properties): void
    {
        foreach ($properties as $property => $propertyClass) {
            $reflection = new ReflectionProperty($class ?? $object, $property);
            $value = $reflection->getValue($object);
            $this->assertInstanceOf($propertyClass, $value);
        }
    }
}
