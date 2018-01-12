<?php

namespace Mocks\MockHttpService\Comparers;

use PHPUnit\Framework\TestCase;

class HttpQueryStringComparerTest extends TestCase
{
    public function testCompare()
    {
        $comparer = new \PhpPact\Mocks\MockHttpService\Comparers\HttpQueryStringComparer();

        $expectedUrl = 'http://localhost/';
        $actualUrl   = 'http://localhost/';
        $results     = $comparer->compare($expectedUrl, $actualUrl);
        $this->assertFalse($results->hasFailure(), 'We expect no failures as there is no query');

        $expectedUrl = 'http://localhost/folder/test.php?x=1&y=2';
        $actualUrl   = 'http://localhost/folder/test.php?x=1&y=2';
        $results     = $comparer->compare($expectedUrl, $actualUrl);
        $this->assertFalse($results->hasFailure(), 'We expect no failures as there is the query is identical');

        $expectedUrl = 'http://localhost/folder/test.php?x=1&y=2';
        $actualUrl   = 'http://localhost/folder/test.php?x=1';
        $results     = $comparer->compare($expectedUrl, $actualUrl);
        $this->assertTrue($results->hasFailure(), 'We expect a failures as there is the query is missing a Y value');

        $expectedUrl = 'x=1&y=2';
        $actualUrl   = 'x=1&y=2';
        $results     = $comparer->compare($expectedUrl, $actualUrl);
        $this->assertFalse($results->hasFailure(), 'We expect no failures as there is the query is identical, while not a full URL');

        $expectedUrl = 'x=1&y=2';
        $actualUrl   = 'y=2&z=3';
        $results     = $comparer->compare($expectedUrl, $actualUrl);
        $this->assertTrue($results->hasFailure(), 'We expect a failures as there is the query is missing an X value and added a Z value, while not a full URL');

        $expectedUrl = 'http://localhost/folder/?x=1&y=2';
        $actualUrl   = 'http://localhost/folder/?x=1&y=2';
        $results     = $comparer->compare($expectedUrl, $actualUrl);
        $this->assertFalse($results->hasFailure(), 'We expect no failures as there is the query is the same and supported at the folder root');

        $expectedUrl = 'http://localhost/folder/?x=1=a&y=2';
        $actualUrl   = 'http://localhost/folder/?x=1%3Da&y=2';
        $results     = $comparer->compare($expectedUrl, $actualUrl);
        $this->assertFalse($results->hasFailure(), 'We expect no failures as the extra = should be encoded during the test');
    }
}
