<?php

namespace PhpPactTest\FFI\Model;

use FFI;
use PhpPact\FFI\Model\ArrayData;
use PHPUnit\Framework\TestCase;

class ArrayDataTest extends TestCase
{
    public function testCreateFromEmptyArray(): void
    {
        $this->assertNull(ArrayData::createFrom([]));
    }

    public function testCreateFromArray(): void
    {
        $branches = ['feature-x', 'master', 'test', 'prod'];
        $arrayData = ArrayData::createFrom($branches);
        $this->assertInstanceOf(ArrayData::class, $arrayData);
        $this->assertSame(count($branches), $arrayData->getSize());
        foreach ($branches as $index => $branch) {
            // @phpstan-ignore argument.type
            $this->assertSame($branch, FFI::string($arrayData->getItems()[$index]));
        }
    }
}
