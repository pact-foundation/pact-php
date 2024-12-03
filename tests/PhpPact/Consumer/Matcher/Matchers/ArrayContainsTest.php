<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Matchers\ArrayContains;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PHPUnit\Framework\TestCase;

class ArrayContainsTest extends TestCase
{
    public function testMissingVariants(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Variants should not be empty');
        new ArrayContains([]);
    }

    public function testFormatJson(): void
    {
        $variants = [
            new Type('string'),
            new Integer(),
        ];
        $matcher = new ArrayContains($variants);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString(
            <<<JSON
            {
                "pact:matcher:type": "arrayContains",
                "variants": [
                    {
                        "pact:matcher:type": "type",
                        "value": "string"
                    },
                    {
                        "pact:matcher:type": "integer",
                        "value": 13
                    }
                ],
                "value": [
                    {
                        "pact:matcher:type": "type",
                        "value": "string"
                    },
                    {
                        "pact:matcher:type": "integer",
                        "value": 13
                    }
                ]
            }
            JSON,
            $jsonEncoded
        );
    }
}
