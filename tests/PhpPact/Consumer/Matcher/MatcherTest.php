<?php

namespace PhpPact\Consumer\Matcher;

use Exception;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    /** @var Matcher */
    private $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Matcher();
    }

    /**
     * @throws Exception
     */
    public function testLikeNoValue()
    {
        $this->expectException(Exception::class);
        $this->matcher->like(null);
    }

    /**
     * @throws Exception
     */
    public function testLike()
    {
        $json = \json_encode($this->matcher->like(12));

        $this->assertEquals('{"contents":12,"json_class":"Pact::SomethingLike"}', $json);
    }

    /**
     * @throws Exception
     */
    public function testEachLikeStdClass()
    {
        $object         = new \stdClass();
        $object->value1 = $this->matcher->like(1);
        $object->value2 = 2;

        $expected = \json_encode([
            'contents' => [
                'value1' => [
                    'contents'   => 1,
                    'json_class' => 'Pact::SomethingLike',
                ],
                'value2' => 2,
            ],
            'json_class' => 'Pact::ArrayLike',
            'min'        => 1,
        ]);

        $actual = \json_encode($this->matcher->eachLike($object, 1));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testEachLikeArray()
    {
        $object = [
            'value1' => $this->matcher->like(1),
            'value2' => 2,
        ];

        $expected = \json_encode([
            'contents' => [
                'value1' => [
                    'contents'   => 1,
                    'json_class' => 'Pact::SomethingLike',
                ],
                'value2' => 2,
            ],
            'json_class' => 'Pact::ArrayLike',
            'min'        => 1,
        ]);

        $actual = \json_encode($this->matcher->eachLike($object, 1));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testRegexNoMatch()
    {
        $this->expectException(Exception::class);
        $this->matcher->regex('SomeWord', 'BadPattern');
    }

    /**
     * @throws Exception
     */
    public function testRegex()
    {
        $expected = [
            'data' => [
                'generate' => 'Games',
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => 'Games|Other',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $actual = $this->matcher->regex('Games', 'Games|Other');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testDate()
    {
        $expected = [
            'data' => [
                'generate' => '2010-01-17',
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^([\\+-]?\\d{4}(?!\\d{2}\\b))((-?)((0[1-9]|1[0-2])(\\3([12]\\d|0[1-9]|3[01]))?|W([0-4]\\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\\d|[12]\\d{2}|3([0-5]\\d|6[1-6])))?)$',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $actual = $this->matcher->dateISO8601('2010-01-17');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider dataProviderForTimeTest
     *
     * @throws Exception
     */
    public function testTime($time)
    {
        $expected = [
            'data' => [
                'generate' => $time,
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^(T\\d\\d:\\d\\d(:\\d\\d)?(\\.\\d+)?([+-][0-2]\\d(?:|:?[0-5]\\d)|Z)?)$',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $actual = $this->matcher->timeISO8601($time);

        $this->assertEquals($expected, $actual);
    }

    public function dataProviderForTimeTest()
    {
        return [
            ['T22:44:30.652Z'],
            ['T22:44:30Z'],
            ['T22:44Z'],
            ['T22:44:30+01:00'],
            ['T22:44:30+0100'],
            ['T22:44:30+01'],
            ['T22:44:30'],
            ['T22:44:30-12:00'],
            ['T22:44:30+0545'],
            ['T22:44:30+14'],
        ];
    }

    /**
     * @dataProvider dataProviderForDateTimeTest
     *
     * @throws Exception
     */
    public function testDateTime($dateTime)
    {
        $expected = [
            'data' => [
                'generate' => $dateTime,
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^\\d{4}-[01]\\d-[0-3]\\dT[0-2]\\d:[0-5]\\d:[0-5]\\d([+-][0-2]\\d(?:|:?[0-5]\\d)|Z)?$',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $actual = $this->matcher->dateTimeISO8601($dateTime);

        $this->assertEquals($expected, $actual);
    }

    public function dataProviderForDateTimeTest()
    {
        return [
            ['2015-08-06T16:53:10+01:00'],
            ['2015-08-06T16:53:10+0100'],
            ['2015-08-06T16:53:10+01'],
            ['2015-08-06T16:53:10Z'],
            ['2015-08-06T16:53:10'],
            ['2015-08-06T16:53:10-12:00'],
            ['2015-08-06T16:53:10+0545'],
            ['2015-08-06T16:53:10+14'],
        ];
    }

    /**
     * @dataProvider dataProviderForDateTimeWithMillisTest
     *
     * @throws Exception
     */
    public function testDateTimeWithMillis($dateTime)
    {
        $expected = [
            'data' => [
                'generate' => $dateTime,
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^\\d{4}-[01]\\d-[0-3]\\dT[0-2]\\d:[0-5]\\d:[0-5]\\d\\.\\d{3}([+-][0-2]\\d(?:|:?[0-5]\\d)|Z)?$',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $actual = $this->matcher->dateTimeWithMillisISO8601($dateTime);

        $this->assertEquals($expected, $actual);
    }

    public function dataProviderForDateTimeWithMillisTest()
    {
        return [
            ['2015-08-06T16:53:10.123+01:00'],
            ['2015-08-06T16:53:10.123+0100'],
            ['2015-08-06T16:53:10.123+01'],
            ['2015-08-06T16:53:10.123Z'],
            ['2015-08-06T16:53:10.123'],
            ['2015-08-06T16:53:10.123-12:00'],
            ['2015-08-06T16:53:10.123+0545'],
            ['2015-08-06T16:53:10.123+14'],
        ];
    }

    /**
     * @throws Exception
     */
    public function testTimestampRFC3339()
    {
        $expected = [
            'data' => [
                'generate' => 'Mon, 31 Oct 2016 15:21:41 -0400',
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^(Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s\\d{2}\\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\\s\\d{4}\\s\\d{2}:\\d{2}:\\d{2}\\s(\\+|-)\\d{4}$',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $actual = $this->matcher->timestampRFC3339('Mon, 31 Oct 2016 15:21:41 -0400');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testInteger()
    {
        $json = \json_encode($this->matcher->integer());

        $this->assertEquals('{"contents":13,"json_class":"Pact::SomethingLike"}', $json);
    }

    /**
     * @throws Exception
     */
    public function testBoolean()
    {
        $json = \json_encode($this->matcher->boolean());

        $this->assertEquals('{"contents":true,"json_class":"Pact::SomethingLike"}', $json);
    }

    /**
     * @throws Exception
     */
    public function testDecimal()
    {
        $json = \json_encode($this->matcher->decimal());

        $this->assertEquals('{"contents":13.01,"json_class":"Pact::SomethingLike"}', $json);
    }

    /**
     * @throws Exception
     */
    public function testHexadecimal()
    {
        $expected = [
            'data' => [
                'generate' => '3F',
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^[0-9a-fA-F]+$',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $this->assertEquals($expected, $this->matcher->hexadecimal());
    }

    /**
     * @throws Exception
     */
    public function testUuid()
    {
        $expected = [
            'data' => [
                'generate' => 'ce118b6e-d8e1-11e7-9296-cec278b6b50a',
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $this->assertEquals($expected, $this->matcher->uuid());
    }

    /**
     * @throws Exception
     */
    public function testIpv4Address()
    {
        $expected = [
            'data' => [
                'generate' => '127.0.0.13',
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^(\\d{1,3}\\.)+\\d{1,3}$',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $this->assertEquals($expected, $this->matcher->ipv4Address());
    }

    /**
     * @throws Exception
     */
    public function testIpv6Address()
    {
        $expected = [
            'data' => [
                'generate' => '::ffff:192.0.2.128',
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$',
                ],
            ],
            'json_class' => 'Pact::Term',
        ];

        $this->assertEquals($expected, $this->matcher->ipv6Address());
    }
}
