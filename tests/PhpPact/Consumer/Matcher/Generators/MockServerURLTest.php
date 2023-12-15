<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\MockServerURL;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PHPUnit\Framework\TestCase;

class MockServerURLTest extends TestCase
{
    private GeneratorInterface $generator;

    protected function setUp(): void
    {
        $this->generator = new MockServerURL('.*(/path)$', 'http://localhost:1234/path');
    }

    public function testType(): void
    {
        $this->assertSame('MockServerURL', $this->generator->getType());
    }

    public function testAttributes(): void
    {
        $attributes = $this->generator->getAttributes();
        $this->assertSame($this->generator, $attributes->getParent());
        $this->assertSame(['regex' => '.*(/path)$', 'example' => 'http://localhost:1234/path'], $attributes->getData());
    }
}
