<?php

namespace Mocks\MockHttpService\Comparers;

use PhpPact\Mocks\MockHttpService\Comparers\HttpStatusCodeComparer;
use PHPUnit\Framework\TestCase;

class HttpStatusCodeComparerTest extends TestCase
{
    public function testCompare()
    {
        $comparer = new HttpStatusCodeComparer();

        $expected = 200;
        $result = $comparer->Compare($expected, '200');
        $this->assertEquals(0, $result->ShallowFailureCount(), "No failures expected");

        $result = $comparer->Compare($expected, '300');
        $this->assertEquals(1, $result->ShallowFailureCount(), "One failure expected");
    }
}
