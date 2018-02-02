<?php

namespace PhpPact\Consumer\Matcher;

use PHPUnit\Framework\TestCase;

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
}
