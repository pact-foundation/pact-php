<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function testType(): void
    {
        $generator = new Date();
        $this->assertSame('Date', $generator->getType());
    }

    /**
     * @testWith [null,         null,     []]
     *           ["yyyy-MM-dd", null,     {"format":"yyyy-MM-dd"}]
     *           [null,         "+1 day", {"expression":"+1 day"}]
     *           ["yyyy-MM-dd", "+1 day", {"format":"yyyy-MM-dd","expression":"+1 day"}]
     */
    public function testAttributes(?string $format, ?string $expression, array $data): void
    {
        $generator = new Date($format, $expression);
        $attributes = $generator->getAttributes();
        $this->assertSame($generator, $attributes->getParent());
        $this->assertSame($data, $attributes->getData());
    }
}
