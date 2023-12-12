<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Exception\InvalidUuidFormatException;
use PhpPact\Consumer\Matcher\Generators\Uuid;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    /**
     * @testWith [null,                    "{\"pact:generator:type\":\"Uuid\"}"]
     *           ["simple",                "{\"pact:generator:type\":\"Uuid\",\"format\":\"simple\"}"]
     *           ["lower-case-hyphenated", "{\"pact:generator:type\":\"Uuid\",\"format\":\"lower-case-hyphenated\"}"]
     *           ["upper-case-hyphenated", "{\"pact:generator:type\":\"Uuid\",\"format\":\"upper-case-hyphenated\"}"]
     *           ["URN",                   "{\"pact:generator:type\":\"Uuid\",\"format\":\"URN\"}"]
     *           ["invalid",               null]
     */
    public function testSerialize(?string $format, ?string $json): void
    {
        if (!$json) {
            $this->expectException(InvalidUuidFormatException::class);
            $this->expectExceptionMessage('Format invalid is not supported. Supported formats are: simple, lower-case-hyphenated, upper-case-hyphenated, URN');
        }
        $uuid = new Uuid($format);
        $this->assertSame($json, json_encode($uuid));
    }
}
