<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PHPUnit\Framework\TestCase;

class RandomBooleanTest extends TestCase
{
    private GeneratorInterface $generator;

    protected function setUp(): void
    {
        $this->generator = new RandomBoolean();
    }

    public function testType(): void
    {
        $this->assertSame('RandomBoolean', $this->generator->getType());
    }

    public function testAttributes(): void
    {
        $attributes = $this->generator->getAttributes();
        $this->assertSame($this->generator, $attributes->getParent());
        $this->assertSame([], $attributes->getData());
    }
}
