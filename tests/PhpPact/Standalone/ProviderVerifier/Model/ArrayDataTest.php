<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model;

use FFI;
use PhpPact\Standalone\ProviderVerifier\Model\ArrayData;
use PHPUnit\Framework\TestCase;

class ArrayDataTest extends TestCase
{
    public function testCreateFromArray()
    {
        $branches = ['feature-x', 'master', 'test', 'prod'];
        $arrayData = ArrayData::createFrom($branches);

        $this->assertSame(count($branches), $arrayData->getSize());
        foreach ($branches as $index => $branch) {
            $this->assertSame($branch, FFI::string($arrayData->getItems()[$index]));
        }
    }
}
