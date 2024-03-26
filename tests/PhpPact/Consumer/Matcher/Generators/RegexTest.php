<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Regex;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    private GeneratorInterface $generator;

    protected function setUp(): void
    {
        $this->generator = new Regex('[\w\d]+');
    }

    public function testType(): void
    {
        $this->assertSame('Regex', $this->generator->getType());
    }

    public function testAttributes(): void
    {
        $attributes = $this->generator->getAttributes();
        $this->assertSame($this->generator, $attributes->getParent());
        $this->assertSame(['regex' => '[\w\d]+'], $attributes->getData());
    }
}
