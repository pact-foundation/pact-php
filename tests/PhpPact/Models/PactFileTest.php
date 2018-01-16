<?php

namespace Models;

use PhpPact\Models\PactFile;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;

class PactFileTest extends TestCase
{
    public function testSetMetadata()
    {
        $f = new PactFile();

        $m = $f->getMetadata();
        $this->assertTrue(isset($m->pactSpecificationVersion), 'Ensure pact specification is set');

        $thrownException = false;

        try {
            $f->setMetadata('fail now');
        } catch (\Exception $e) {
            $thrownException = true;
        }
        $this->assertTrue($thrownException, 'Ensure an exception is thrown if the proper object is not passed');

        $thrownException = false;

        try {
            $o = new \stdClass();
            $f->setMetadata($o);
        } catch (\Exception $e) {
            $thrownException = true;
        }
        $this->assertTrue($thrownException, 'Ensure an exception is thrown if the proper object is without a pactSpecificationVersion');

        $thrownException = false;

        try {
            $o                           = new \stdClass();
            $o->pactSpecificationVersion = '2.0';
            $m                           = $f->setMetadata($o);
        } catch (\Exception $e) {
            $thrownException = true;
        }
        $this->assertFalse($thrownException, 'A proper object is set');
        $this->assertEquals('2.0', $m->pactSpecificationVersion, 'Checks that the metadata we checked appropriately');

        $thrownException = false;

        try {
            $o                                     = new \stdClass();
            $o->metadata                           = new \stdClass();
            $o->metadata->pactSpecificationVersion = '3.0';
            $m                                     = $f->setMetadata($o);
        } catch (\Exception $e) {
            $thrownException = true;
        }
        $this->assertFalse($thrownException, 'A proper object is set');
        $this->assertEquals('3.0', $m->pactSpecificationVersion, 'Checks that the metadata we checked appropriately');
    }
}
