<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Time;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    public function testType(): void
    {
        $generator = new Time();
        $this->assertSame('Time', $generator->getType());
    }

    /**
     * @testWith [null,       null,      []]
     *           ["HH:mm:ss", null,      {"format":"HH:mm:ss"}]
     *           [null,       "+1 hour", {"expression":"+1 hour"}]
     *           ["HH:mm:ss", "+1 hour", {"format":"HH:mm:ss","expression":"+1 hour"}]
     */
    public function testAttributes(?string $format, ?string $expression, array $data): void
    {
        $generator = new Time($format, $expression);
        $attributes = $generator->getAttributes();
        $this->assertSame($generator, $attributes->getParent());
        $this->assertSame($data, $attributes->getData());
    }
}
