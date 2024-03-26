<?php

namespace PhpPactTest\Consumer\Model\Body;

use PhpPact\Consumer\Model\Body\Text;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    private Text $text;

    public function setUp(): void
    {
        $this->text = new Text('example', 'text/plain');
    }

    public function testContents(): void
    {
        $this->assertSame('example', $this->text->getContents());
        $this->text->setContents($csv = 'column1,column2,column3');
        $this->assertSame($csv, $this->text->getContents());
    }

    public function testContentType(): void
    {
        $this->assertSame('text/plain', $this->text->getContentType());
        $this->text->setContentType('application/json');
        $this->assertSame('application/json', $this->text->getContentType());
    }
}
