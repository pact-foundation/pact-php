<?php

namespace PhpPactTest\FFI\Model;

use FFI;
use PhpPact\FFI\Exception\EmptyBinaryFileNotSupportedException;
use PhpPact\FFI\Model\BinaryData;
use PHPUnit\Framework\TestCase;

class BinaryDataTest extends TestCase
{
    public function testCreateFromEmptyString(): void
    {
        $this->expectException(EmptyBinaryFileNotSupportedException::class);
        BinaryData::createFrom('');
    }

    public function testCreateBinaryString(): void
    {
        $path = __DIR__ . '/../../../_resources/image.jpg';
        $contents = file_get_contents($path);
        $this->assertIsString($contents);
        $length = \strlen($contents);

        $binaryData = BinaryData::createFrom($contents);
        $cData = $binaryData->getValue();

        $this->assertSame($length, FFI::sizeof($cData));
        $this->assertSame($length, $binaryData->getSize());
        $this->assertEquals($contents, (string) $binaryData);
    }
}
