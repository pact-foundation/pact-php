<?php

namespace PhpPactTest\Consumer\Matcher\Model;

use PhpPact\Consumer\Matcher\Exception\AttributeConflictException;
use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
//use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    public function testParent(): void
    {
        $subject = new Attributes($generator = new RandomBoolean());
        $this->assertSame($generator, $subject->getParent());

        //$subject = new Attributes($matcher = new NullValue());
        //$this->assertSame($matcher, $subject->getParent());
    }

    public function testData(): void
    {
        $subject = new Attributes(new RandomBoolean(), $data = ['key' => 'value']);
        $this->assertSame($data, $subject->getData());
        $this->assertFalse($subject->has('new key'));
        $this->assertNull($subject->get('new key'));
        $this->assertTrue($subject->has('key'));
        $this->assertSame('value', $subject->get('key'));
    }

    public function testMergeConflict(): void
    {
        $attributes = new Attributes(new RandomBoolean(), ['key' => 'value 1']);
        $this->expectException(AttributeConflictException::class);
        //$this->expectExceptionMessage("Attribute 'key' of generator 'RandomBoolean' and matcher 'null' are conflict");
        $this->expectExceptionMessage("Attribute 'key' of generator 'RandomBoolean' and generator 'RandomBoolean' are conflict");
        $attributes->merge(new Attributes(new RandomBoolean(), ['key' => 'value 2']));
    }

    public function testMerge(): void
    {
        $parent = new RandomBoolean();
        $attributes = new Attributes($parent, ['key' => 'value', 'key 2' => 123]);
        $merged = $attributes->merge(new Attributes(new RandomBoolean(), ['key' => 'value', 'key 3' => ['value 1', 'value 2']]));
        $this->assertSame($parent, $merged->getParent());
        $this->assertSame(['key' => 'value', 'key 2' => 123, 'key 3' => ['value 1', 'value 2']], $merged->getData());
    }
}
