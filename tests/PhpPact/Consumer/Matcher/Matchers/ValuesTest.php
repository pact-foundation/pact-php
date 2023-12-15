<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Values;
use PHPUnit\Framework\TestCase;

class ValuesTest extends TestCase
{
    /**
     * @testWith [["value 1", "value 2"],                   "{\"pact:matcher:type\":\"values\",\"value\":[\"value 1\",\"value 2\"]}"]
     *           [{"key 1": "value 1", "key 2": "value 2"}, "{\"pact:matcher:type\":\"values\",\"value\":{\"key 1\":\"value 1\",\"key 2\":\"value 2\"}}"]
     */
    public function testSerialize(array $values, string $json): void
    {
        $array = new Values($values);
        $this->assertSame($json, json_encode($array));
    }
}
