<?php

namespace PhpPact\Consumer\Matcher;

use PHPUnit\Framework\TestCase;

class LikeMatcherTest extends TestCase
{
    public function testMatcherWithSingleArray()
    {
        $matcher = new LikeMatcher(['name' => 'Games']);
        $json    = \json_encode($matcher);
        $this->assertEquals('{"contents":{"name":"Games"},"json_class":"Pact::ArrayLike","min":1}', $json);
    }

    public function testMatcherWithSingleValue()
    {
        $matcher = new LikeMatcher('Games');
        $json    = \json_encode($matcher);
        $this->assertEquals('{"contents":"Games","json_class":"Pact::SomethingLike"}', $json);
    }

    public function testMatcherWithMultipleValuesInArray()
    {
        $matcher = new LikeMatcher(['name' => 'Games', 'type' => 'Baseball']);
        $json    = \json_encode($matcher);
        $this->assertEquals('{"contents":{"name":"Games","type":"Baseball"},"json_class":"Pact::ArrayLike","min":1}', $json);
    }

    public function testMatcherWithMin()
    {
        $matcher = new LikeMatcher(['name' => 'Games'], 5);
        $json    = \json_encode($matcher);
        $this->assertEquals('{"contents":{"name":"Games"},"json_class":"Pact::ArrayLike","min":5}', $json);
    }

    public function testMatcherWithMax()
    {
        $matcher = new LikeMatcher(['name' => 'Games'], null, 10);
        $json    = \json_encode($matcher);
        $this->assertEquals('{"contents":{"name":"Games"},"json_class":"Pact::ArrayLike","min":1,"max":10}', $json);
    }

    public function testMatcherStdObject()
    {
        $category1       = new \stdClass();
        $category1->name = new RegexMatcher('Games', '[gbBG]');

        $body = [
            'results' => new LikeMatcher([
                $category1
            ])
        ];

        $json    = \json_encode($body);
        $this->assertEquals('{"results":{"contents":[{"name":{"json_class":"Pact::Term","data":{"generate":"Games","matcher":{"json_class":"Regexp","o":0,"s":"[gbBG]"}}}}],"json_class":"Pact::ArrayLike","min":1}}', $json);
    }
}
