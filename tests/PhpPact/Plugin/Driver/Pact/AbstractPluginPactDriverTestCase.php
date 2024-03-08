<?php

namespace PhpPactTest\Plugin\Driver\Pact;

use PhpPact\Consumer\Driver\Exception\MissingPactException;
use PhpPact\Plugin\Driver\Pact\AbstractPluginPactDriver;
use PhpPact\Plugin\Exception\PluginNotSupportedBySpecificationException;
use PhpPactTest\Consumer\Driver\Pact\PactDriverTest;
use PHPUnit\Framework\Attributes\TestWith;

abstract class AbstractPluginPactDriverTestCase extends PactDriverTest
{
    #[TestWith(['1.0.0', self::SPEC_V1,   false])]
    #[TestWith(['1.1.0', self::SPEC_V1_1, false])]
    #[TestWith(['2.0.0', self::SPEC_V2,   false])]
    #[TestWith(['3.0.0', self::SPEC_V3,   false])]
    #[TestWith(['4.0.0', self::SPEC_V4,   true])]
    public function testSetUpUsingPlugin(string $version, int $specificationHandle, bool $supported): void
    {
        $this->assertConfig(null, $version);
        $calls = $supported ? [
            ['pactffi_new_pact', $this->consumer, $this->provider, $this->pactHandle],
            ['pactffi_with_specification', $this->pactHandle, $specificationHandle, null],
            ['pactffi_using_plugin', $this->pactHandle, $this->getPluginName(), null, null],
        ] : [
            ['pactffi_new_pact', $this->consumer, $this->provider, $this->pactHandle],
            ['pactffi_with_specification', $this->pactHandle, $specificationHandle, null],
        ];
        $this->assertClientCalls($calls);
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
        $calls = [
            ['pactffi_new_pact', $this->consumer, $this->provider, $this->pactHandle],
            ['pactffi_with_specification', $this->pactHandle, self::SPEC_V4, null],
            ['pactffi_using_plugin', $this->pactHandle, $this->getPluginName(), null, null],
            ['pactffi_cleanup_plugins', $this->pactHandle, null],
            ['pactffi_free_pact_handle', $this->pactHandle, null],
        ];
        $this->assertClientCalls($calls);
        $this->driver = $this->createPactDriver();
        $this->driver->setUp();
        $this->driver->cleanUp();
    }

    public function testCleanUpPluginWithoutPact(): void
    {
        $this->expectException(MissingPactException::class);
        $this->driver->cleanUp();
    }

    abstract protected function createPactDriver(): AbstractPluginPactDriver;

    abstract protected function getPluginName(): string;
}
