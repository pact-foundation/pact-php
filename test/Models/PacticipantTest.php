<?php

namespace Models;

use PhpPact\Models\Pacticipant;
use PHPUnit\Framework\TestCase;

class PacticipantTest extends TestCase
{
    public function testSetName()
    {
        $p = new \PhpPact\Models\Pacticipant();

        $actual = $p->setName("MyName");
        $this->assertEquals("MyName", $actual, "Name set appropriately");

        $actual = $p->getName();
        $this->assertEquals("MyName", $actual, "Name set appropriately.  Getter was used");

        $obj = new \stdClass();
        $obj->Name = "MyObjectName";
        $actual = $p->setName("MyObjectName");
        $this->assertEquals("MyObjectName", $actual, "Name set appropriately when in an object");
    }
}
