<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PHPUnit\Framework\TestCase;

class RandomStringTest extends TestCase
{
    private GeneratorInterface $generator;

    protected function setUp(): void
    {
        $this->generator = new RandomString(11);
    }

    public function testType(): void
    {
        $this->assertSame('RandomString', $this->generator->getType());
    }

    public function testAttributes(): void
    {
        $attributes = $this->generator->getAttributes();
        $this->assertSame($this->generator, $attributes->getParent());
        $this->assertSame(['size' => 11], $attributes->getData());
    }
}
