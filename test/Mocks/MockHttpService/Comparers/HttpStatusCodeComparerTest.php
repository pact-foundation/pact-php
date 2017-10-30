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
        $result = $comparer->compare($expected, '200');
        $this->assertEquals(0, $result->shallowFailureCount(), "No failures expected");

        $result = $comparer->compare($expected, '300');
        $this->assertEquals(1, $result->shallowFailureCount(), "One failure expected");
    }
}
