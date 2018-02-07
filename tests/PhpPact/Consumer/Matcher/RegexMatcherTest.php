<?php

namespace PhpPact\Consumer\Matcher;

use PHPUnit\Framework\TestCase;

class RegexMatcherTest extends TestCase
{
    public function testMatcherWithSingleArray()
    {
        $matcher = new RegexMatcher(['name' => 'Games'], 'Games|Book Clubs');
        $json    = \json_encode($matcher);
        $this->assertEquals('{"contents":{"json_class":"Pact::ArrayLike","name":{"json_class":"Pact::Term","data":{"generate":"Games","matcher":{"json_class":"Regexp","o":0,"s":"Games|Book Clubs"}}}}}', $json);
    }

    public function testMatcherWithSingleValue()
    {
        $matcher = new RegexMatcher('Games', 'Games|Book Clubs');
        $json    = \json_encode($matcher);
        $this->assertEquals('{"json_class":"Pact::Term","data":{"generate":"Games","matcher":{"json_class":"Regexp","o":0,"s":"Games|Book Clubs"}}}', $json);
    }

    public function testMatcherWithMultipleValuesInArray()
    {
        $matcher = new RegexMatcher(['name' => 'Games', 'type' => 'Baseball'], 'Games|Book Clubs');
        $json    = \json_encode($matcher);
        $this->assertEquals('{"contents":{"json_class":"Pact::ArrayLike","name":{"json_class":"Pact::Term","data":{"generate":"Games","matcher":{"json_class":"Regexp","o":0,"s":"Games|Book Clubs"}}},"type":{"json_class":"Pact::Term","data":{"generate":"Baseball","matcher":{"json_class":"Regexp","o":0,"s":"Games|Book Clubs"}}}}}', $json);
    }
}
