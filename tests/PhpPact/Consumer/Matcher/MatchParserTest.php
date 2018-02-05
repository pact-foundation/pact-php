<?php

namespace PhpPact\Consumer\Matcher;

use DeepCopy\TypeMatcher\TypeMatcher;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Test the Matcher parser.
 * Class MatchParserTest
 */
class MatchParserTest extends TestCase
{
    /**
     * Test parsing without any matchers.
     */
    public function testNoMatcher()
    {
        $parser = new MatchParser();
        $body   = [
            'Data' => [
                'Stuff' => [
                    'item1' => 2,
                    'Something'
                ]
            ]
        ];

        $matchers = $parser->parse($body);

        $this->assertNull($matchers);
    }

    /**
     * Test mixing both Like and Regex matchers.
     */
    public function testLikeAndRegex()
    {
        $parser = new MatchParser();
        $body   = [
            'Data' => [
                'Stuff' => [
                    'Here' => new LikeMatcher('42')
                ]
            ],
            'Value' => new RegexMatcher('Thing', 'SomePattern')
        ];

        $matchers = $parser->parse($body);

        $this->assertArrayHasKey('$.body.Data.Stuff.Here', $matchers);
        $this->assertInstanceOf(LikeMatcher::class, $matchers['$.body.Data.Stuff.Here']);
        $this->assertArrayHasKey('$.body.Value', $matchers);
        $this->assertInstanceOf(RegexMatcher::class, $matchers['$.body.Value']);
    }

    /**
     * Verify a simple flat array matcher.
     */
    public function testParseFlatArray()
    {
        $parser = new MatchParser();
        $body   = [
            new LikeMatcher([
                'id'        => 4,
                'firstName' => 'John',
                'lastName'  => 'Smith'
            ])
        ];

        $matchers = $parser->parse($body);

        $this->assertArrayHasKey('$.body[*].[*]', $matchers);
        $this->assertInstanceOf(LikeMatcher::class, $matchers['$.body[*].[*]']);

        $this->assertEquals(4, $body[0]['id']);
        $this->assertEquals('John', $body[0]['firstName']);
        $this->assertEquals('Smith', $body[0]['lastName']);
    }

    /**
     * Verify that a matcher works with a list.
     */
    public function testParseList()
    {
        $body = [
            'dates' => new RegexMatcher([
                '01/11/2017',
                '04/17/2012',
                '08/06/1987'
            ], '^(0[1-9]|1[012])[/](0[1-9]|[12][0-9]|3[01])[/](19|20)\d\d$')
        ];

        $parser   = new MatchParser();
        $matchers = $parser->parse($body);

        $this->assertArrayHasKey('$.body.dates[*]', $matchers);
        $this->assertInstanceOf(RegexMatcher::class, $matchers['$.body.dates[*]']);
    }

    /**
     * Verify that we can still use a matcher with an array of data.
     */
    public function testParseArrayWithMatcher()
    {
        $body = [
            'data' => [
                new LikeMatcher([
                    'firstItem'  => 1,
                    'secondItem' => 'Stuff'
                ]),
                [
                    'firstItem'  => 2,
                    'secondItem' => 'Other Stuff'
                ]
            ]
        ];

        $parser   = new MatchParser();
        $matchers = $parser->parse($body);

        $this->assertArrayHasKey('$.body.data[*].[*]', $matchers);
        $this->assertInstanceOf(LikeMatcher::class, $matchers['$.body.data[*].[*]']);
    }

    public function testParseStdObject()
    {
        $body = new \stdClass();

        $first             = new \stdClass();
        $first->firstItem  = 1;
        $first->secondItem = 'Stuff';
        $body->data[]      = new LikeMatcher($first);

        $second             = new \stdClass();
        $second->firstItem  = 2;
        $second->secondItem = 'Other Stuff';
        $body->data[]       = $second;

        $parser   = new MatchParser();
        $matchers = $parser->parse($body);

        $this->assertArrayHasKey('$.body.data[*].[*]', $matchers);
        $this->assertInstanceOf(LikeMatcher::class, $matchers['$.body.data[*].[*]']);
    }

    public function testParseStdObject2()
    {
        $category1            = new stdClass();
        $category1->name      = new RegexMatcher('Games', 'Games|Book Clubs');
        $category1->sort_name = new LikeMatcher(['Games', 'Book Clubs']);
        $category1->id        = new TypeMatcher(17);
        $category1->shortname = new LikeMatcher(['Games', 'Book Clubs']);

        $body[] = $category1;

        $category2            = new stdClass();
        $category2->name      = 'Book Clubs';
        $category2->sort_name = 'Book Clubs';
        $category2->id        = 18;
        $category2->shortname = 'Book Clubs';

        $body[] = $category2;

        $parser   = new MatchParser();
        $matchers = $parser->parse($body);

        $this->assertArrayHasKey('$.body.name', $matchers);
        $this->assertInstanceOf(RegexMatcher::class, $matchers['$.body.name']);

        $this->assertArrayHasKey('$.body.sort_name[*]', $matchers);
        $this->assertInstanceOf(LikeMatcher::class, $matchers['$.body.sort_name[*]']);

        $this->assertArrayHasKey('$.body.shortname[*]', $matchers);
        $this->assertInstanceOf(LikeMatcher::class, $matchers['$.body.shortname[*]']);
    }
}
