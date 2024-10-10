<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Exception\InvalidUuidFormatException;
use PhpPact\Consumer\Matcher\Generators\Uuid;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    public function testInvalidFormat(): void
    {
        $this->expectException(InvalidUuidFormatException::class);
        $this->expectExceptionMessage('Format invalid is not supported. Supported formats are: simple, lower-case-hyphenated, upper-case-hyphenated, URN');
        new Uuid('invalid');
    }

    #[TestWith([null, '{"pact:generator:type":"Uuid"}'])]
    #[TestWith(['simple', '{"pact:generator:type":"Uuid","format":"simple"}'])]
    #[TestWith(['lower-case-hyphenated', '{"pact:generator:type":"Uuid","format":"lower-case-hyphenated"}'])]
    #[TestWith(['upper-case-hyphenated', '{"pact:generator:type":"Uuid","format":"upper-case-hyphenated"}'])]
    #[TestWith(['URN', '{"pact:generator:type":"Uuid","format":"URN"}'])]
    public function testAttributes(?string $format, string $json): void
    {
        $generator = new Uuid($format);
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame($json, $result);
    }
}
