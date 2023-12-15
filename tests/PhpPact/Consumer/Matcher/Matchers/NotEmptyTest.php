<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\NotEmpty;
use PHPUnit\Framework\TestCase;

class NotEmptyTest extends TestCase
{
    public function testSerialize(): void
    {
        $array = new NotEmpty(['some text']);
        $this->assertSame(
            '{"pact:matcher:type":"notEmpty","value":["some text"]}',
            json_encode($array)
        );
    }
}
