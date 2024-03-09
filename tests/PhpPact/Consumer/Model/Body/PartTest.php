<?php

namespace PhpPactTest\Consumer\Model\Body;

use PhpPact\Consumer\Model\Body\Part;
use PHPUnit\Framework\TestCase;

class PartTest extends TestCase
{
    private Part $part;

    public function setUp(): void
    {
        $this->part = new Part('/path/to/file.txt', 'id', 'text/plain');
    }

    public function testPart(): void
    {
        $this->assertSame('/path/to/file.txt', $this->part->getPath());
        $this->part->setPath('/other/path/to/file.jpg');
        $this->assertSame('/other/path/to/file.jpg', $this->part->getPath());
    }

    public function testName(): void
    {
        $this->assertSame('id', $this->part->getName());
        $this->part->setName('profilePicture');
        $this->assertSame('profilePicture', $this->part->getName());
    }

    public function testContentType(): void
    {
        $this->assertSame('text/plain', $this->part->getContentType());
        $this->part->setContentType('application/json');
        $this->assertSame('application/json', $this->part->getContentType());
    }
}
