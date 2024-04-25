<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Time;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    public function testType(): void
    {
        $generator = new Time();
        $this->assertSame('Time', $generator->getType());
    }

    /**
     * @param array<string, string> $data
     */
    #[TestWith([null, null, []])]
    #[TestWith(['HH:mm:ss', null, ['format' => 'HH:mm:ss']])]
    #[TestWith([null, '+1 hour', ['expression' => '+1 hour']])]
    #[TestWith(['HH:mm:ss', '+1 hour', ['format' => 'HH:mm:ss', 'expression' => '+1 hour']])]
    public function testAttributes(?string $format, ?string $expression, array $data): void
    {
        $generator = new Time($format, $expression);
        $attributes = $generator->getAttributes();
        $this->assertSame($generator, $attributes->getParent());
        $this->assertSame($data, $attributes->getData());
    }
}
