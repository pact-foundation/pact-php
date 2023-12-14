<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PHPUnit\Framework\TestCase;

class ProviderStateTest extends TestCase
{
    private GeneratorInterface $generator;

    protected function setUp(): void
    {
        $this->generator = new ProviderState('/products/${id}');
    }

    public function testType(): void
    {
        $this->assertSame('ProviderState', $this->generator->getType());
    }

    public function testAttributes(): void
    {
        $attributes = $this->generator->getAttributes();
        $this->assertSame($this->generator, $attributes->getParent());
        $this->assertSame(['expression' => '/products/${id}'], $attributes->getData());
    }
}
