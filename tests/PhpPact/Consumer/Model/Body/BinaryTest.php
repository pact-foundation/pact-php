<?php

namespace PhpPactTest\Consumer\Model\Body;

use PhpPact\Consumer\Exception\BinaryFileNotExistException;
use PhpPact\Consumer\Model\Body\Binary;
use PHPUnit\Framework\TestCase;

class BinaryTest extends TestCase
{
    public function testGetPath(): void
    {
        $body = new Binary('/path/to/file1.jpg', 'image/jpeg');
        $this->assertSame('/path/to/file1.jpg', $body->getPath());
        $body->setPath('/other/path/to/file2.jpg');
        $this->assertSame('/other/path/to/file2.jpg', $body->getPath());
    }

    public function testGetContentType(): void
    {
        $body = new Binary('/path/to/text.txt', 'plain/text');
        $this->assertSame('plain/text', $body->getContentType());
        $body->setContentType('text/csv');
        $this->assertSame('text/csv', $body->getContentType());
    }

    public function testGetDataFromInvalidFilePath(): void
    {
        $path = __DIR__ . '/../../../../_resources/invalid.jpg';
        $body = new Binary($path, 'image/jpeg');
        $this->expectException(BinaryFileNotExistException::class);
        $this->expectExceptionMessage("File $path does not exist");
        $body->getData();
    }

    public function testGetData(): void
    {
        $path = __DIR__ . '/../../../../_resources/image.jpg';
        $body = new Binary($path, 'image/jpeg');
        $data = $body->getData();

        $this->assertEquals(file_get_contents($path), (string) $data);
    }
}
