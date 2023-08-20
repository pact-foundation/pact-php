<?php

namespace PhpPactTest\FFI\Model;

use FFI;
use FFI\CData;
use PhpPact\FFI\Model\StringData;
use PHPUnit\Framework\TestCase;

class StringDataTest extends TestCase
{
    public function testCreateNullTerminatedString()
    {
        $value = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
        $stringData = StringData::createFrom($value);
        $cData = $stringData->getValue();
        $size = \strlen($value) + 1;

        $this->assertSame($size, FFI::sizeof($cData));
        $this->assertSame($value, $this->cDataToString($cData, $size - 1)); // ignore null
    }

    public function testCreateBinaryString()
    {
        $value = file_get_contents(__DIR__ . '/../../../_resources/image.jpg');
        $stringData = StringData::createFrom($value, false);
        $cData = $stringData->getValue();
        $size = \strlen($value);

        $this->assertSame($size, FFI::sizeof($cData));
        $this->assertEquals($value, $this->cDataToString($cData, $size));
    }

    private function cDataToString(CData $cData, int $size): string
    {
        $result = '';
        for ($index = 0; $index < $size; $index++) {
            $result .= chr($cData[$index]); // @phpstan-ignore-line
        }

        return $result;
    }
}
