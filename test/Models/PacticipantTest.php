<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 6/28/2017
 * Time: 3:54 PM
 */

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
