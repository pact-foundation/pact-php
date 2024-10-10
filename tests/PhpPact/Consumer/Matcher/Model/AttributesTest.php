<?php

namespace PhpPactTest\Consumer\Matcher\Model;

use PhpPact\Consumer\Matcher\Exception\AttributeConflictException;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    public function testData(): void
    {
        $subject = new Attributes(['key' => 'value']);
        $this->assertFalse($subject->has('new key'));
        $this->assertNull($subject->get('new key'));
        $this->assertTrue($subject->has('key'));
        $this->assertSame('value', $subject->get('key'));
    }

    public function testJsonSerialize(): void
    {
        $subject = new Attributes(['key' => 'value']);
        $this->assertSame('{"key":"value"}', json_encode($subject));
    }

    public function testMergeConflict(): void
    {
        $attributes = new Attributes(['key' => 'value 1']);
        $this->expectException(AttributeConflictException::class);
        $this->expectExceptionMessage("Can not merge attributes: Values of attribute 'key' are conflict");
        $attributes->merge(new Attributes(['key' => 'value 2']));
    }

    public function testMerge(): void
    {
        $attributes = new Attributes(['key' => 'value', 'key 2' => 123]);
        $merged = $attributes->merge(new Attributes(['key' => 'value', 'key 3' => ['value 1', 'value 2']]));
        $this->assertSame(['key' => 'value', 'key 2' => 123, 'key 3' => ['value 1', 'value 2']], iterator_to_array($merged));
    }
}
