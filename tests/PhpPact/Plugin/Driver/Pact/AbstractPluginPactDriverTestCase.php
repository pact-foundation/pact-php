<?php

namespace PhpPactTest\Plugin\Driver\Pact;

use PhpPact\Plugin\Driver\Pact\AbstractPluginPactDriver;
use PhpPact\Plugin\Exception\PluginNotSupportedBySpecificationException;
use PhpPactTest\Consumer\Driver\Pact\PactDriverTest;
use PHPUnit\Framework\Attributes\TestWith;

abstract class AbstractPluginPactDriverTestCase extends PactDriverTest
{
    #[TestWith(['1.0.0', self::SPEC_V1,   false, 0])]
    #[TestWith(['1.1.0', self::SPEC_V1_1, false, 0])]
    #[TestWith(['2.0.0', self::SPEC_V2,   false, 0])]
    #[TestWith(['3.0.0', self::SPEC_V3,   false, 0])]
    #[TestWith(['4.0.0', self::SPEC_V4,   true,  0])]
    #[TestWith(['4.0.0', self::SPEC_V4,   true,  1])]
    #[TestWith(['4.0.0', self::SPEC_V4,   true,  2])]
    #[TestWith(['4.0.0', self::SPEC_V4,   true,  3])]
    #[TestWith(['4.0.0', self::SPEC_V4,   true,  4])]
    public function testSetUpUsingPlugin(string $version, int $specificationHandle, bool $supported, int $error): void
    {
        $this->assertConfig(null, $version);
        $this->expectsNewPact($this->consumer, $this->provider, $this->pactHandle);
        $this->expectsWithSpecification($this->pactHandle, $specificationHandle, true);
        $this->expectsUsingPlugin($this->pactHandle, $this->getPluginName(), null, $error, $supported);
        if (!$supported) {
            $this->expectException(PluginNotSupportedBySpecificationException::class);
            $this->expectExceptionMessage(sprintf(
                'Plugin is not supported by specification %s, use 4.0.0 or above',
                $version,
            ));
        }
        $this->driver = $this->createPactDriver();
        $this->driver->setUp();
    }

    public function testCleanUpPlugin(): void
    {
        $this->assertConfig(null, '4.0.0');
        $this->expectsNewPact($this->consumer, $this->provider, $this->pactHandle);
        $this->expectsWithSpecification($this->pactHandle, self::SPEC_V4, true);
        $this->expectsUsingPlugin($this->pactHandle, $this->getPluginName(), null, 0, true);
        $this->expectsCleanupPlugins($this->pactHandle);
        $this->expectsFreePactHandle($this->pactHandle, 0);
        $this->driver = $this->createPactDriver();
        $this->driver->setUp();
        $this->driver->cleanUp();
    }

    public function testCleanUpPluginWithoutPact(): void
    {
        $this->expectNotToPerformAssertions();
        $this->driver->cleanUp();
    }

    abstract protected function createPactDriver(): AbstractPluginPactDriver;

    abstract protected function getPluginName(): string;
}
