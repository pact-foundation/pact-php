<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    public function testType(): void
    {
        $generator = new DateTime();
        $this->assertSame('DateTime', $generator->getType());
    }

    /**
     * @testWith [null,                    null,     []]
     *           ["yyyy-MM-dd'T'HH:mm:ss", null,     {"format":"yyyy-MM-dd'T'HH:mm:ss"}]
     *           [null,                    "+1 day", {"expression":"+1 day"}]
     *           ["yyyy-MM-dd'T'HH:mm:ss", "+1 day", {"format":"yyyy-MM-dd'T'HH:mm:ss","expression":"+1 day"}]
     */
    public function testAttributes(?string $format, ?string $expression, array $data): void
    {
        $generator = new DateTime($format, $expression);
        $attributes = $generator->getAttributes();
        $this->assertSame($generator, $attributes->getParent());
        $this->assertSame($data, $attributes->getData());
    }
}
