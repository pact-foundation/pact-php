<?php

namespace PhpPactTest\Consumer\Model\Body;

use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Part;
use PHPUnit\Framework\TestCase;

class MultipartTest extends TestCase
{
    private Multipart $multipart;

    public function setUp(): void
    {
        $this->multipart = new Multipart([], '2a8ae6ad');
    }

    public function testParts(): void
    {
        $this->assertSame([], $this->multipart->getParts());
        $this->multipart->setParts($parts = [
            new Part('/path/to/file1.txt', 'file1', 'text/plain'),
            new Part('/path/to/file2.csv', 'file2', 'text/csv'),
        ]);
        $this->assertSame($parts, $this->multipart->getParts());
    }

    public function testBoundary(): void
    {
        $this->assertSame('2a8ae6ad', $this->multipart->getBoundary());
    }
}
