<?php

namespace PhpPactTest\Consumer\Matcher;

use Exception;
use PhpPact\Consumer\Matcher\HttpStatus;
use PhpPact\Consumer\Matcher\Matcher;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    private Matcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Matcher();
    }

    /**
     * @throws Exception
     */
    public function testLikeNull(): void
    {
        $json = \json_encode($this->matcher->like(null));

        $this->assertEquals('{"value":null,"pact:matcher:type":"type"}', $json);
    }

    /**
     * @throws Exception
     */
    public function testLike()
    {
        $json = \json_encode($this->matcher->like(12));

        $this->assertEquals('{"value":12,"pact:matcher:type":"type"}', $json);
    }

    /**
     * @dataProvider dataProviderForEachLikeTest
     */
    public function testEachLike(object|array $value)
    {
        $expected = \json_encode([
            'value' => [
                [
                    'value1' => [
                        'value'             => 1,
                        'pact:matcher:type' => 'type',
                    ],
                    'value2' => 2,
                ],
            ],
            'pact:matcher:type' => 'type',
            'min'               => 1,
        ]);

        $actual = \json_encode($this->matcher->eachLike($value));

        $this->assertEquals($expected, $actual);
    }

    public function dataProviderForEachLikeTest()
    {
        $value1Matcher = [
            'value'             => 1,
            'pact:matcher:type' => 'type',
        ];

        $object         = new \stdClass();
        $object->value1 = $value1Matcher;
        $object->value2 = 2;

        $array = [
            'value1' => $value1Matcher,
            'value2' => 2,
        ];

        return [
            [$object],
            [$array],
        ];
    }

    /**
     * @dataProvider dataProviderForEachLikeTest
     */
    public function testAtLeastLike(object|array $value)
    {
        $eachValueMatcher = [
            'value1' => [
                'value'             => 1,
                'pact:matcher:type' => 'type',
            ],
            'value2' => 2,
        ];
        $expected = \json_encode([
            'value' => [
                $eachValueMatcher,
                $eachValueMatcher,
            ],
            'pact:matcher:type' => 'type',
            'min'               => 2,
        ]);

        $actual = \json_encode($this->matcher->atLeastLike($value, 2));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider dataProviderForEachLikeTest
     */
    public function testAtMostLike(object|array $value)
    {
        $expected = \json_encode([
            'value' => [
                [
                    'value1' => [
                        'value'             => 1,
                        'pact:matcher:type' => 'type',
                    ],
                    'value2' => 2,
                ],
            ],
            'pact:matcher:type' => 'type',
            'max'               => 2,
        ]);

        $actual = \json_encode($this->matcher->atMostLike($value, 2));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testConstrainedArrayLikeCountLessThanMin()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('constrainedArrayLike has a minimum of 2 but 1 elements where requested.' .
        ' Make sure the count is greater than or equal to the min.');
        $this->matcher->constrainedArrayLike('text', 2, 4, 1);
    }

    /**
     * @throws Exception
     */
    public function testConstrainedArrayLikeCountLargerThanMax()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('constrainedArrayLike has a maximum of 5 but 7 elements where requested.' .
        ' Make sure the count is less than or equal to the max.');
        $this->matcher->constrainedArrayLike('text', 3, 5, 7);
    }

    /**
     * @dataProvider dataProviderForEachLikeTest
     */
    public function testConstrainedArrayLike(object|array $value)
    {
        $eachValueMatcher = [
            'value1' => [
                'value'             => 1,
                'pact:matcher:type' => 'type',
            ],
            'value2' => 2,
        ];
        $expected = \json_encode([
            'min'               => 2,
            'max'               => 4,
            'pact:matcher:type' => 'type',
            'value' => [
                $eachValueMatcher,
                $eachValueMatcher,
                $eachValueMatcher,
            ],
        ]);

        $actual = \json_encode($this->matcher->constrainedArrayLike($value, 2, 4, 3));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testRegexNoMatch()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The pattern BadPattern is not valid for value SomeWord. Failed with error code 0.');
        $this->matcher->regex('SomeWord', 'BadPattern');
    }

    /**
     * @throws Exception
     */
    public function testRegex()
    {
        $expected = [
            'value'             => 'Games',
            'regex'             => 'Games|Other',
            'pact:matcher:type' => 'regex',
        ];

        $actual = $this->matcher->regex('Games', 'Games|Other');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testRegexMultiValues()
    {
        $expected = [
            'value'             => [1, 23],
            'regex'             => '\d+',
            'pact:matcher:type' => 'regex',
        ];

        $actual = $this->matcher->regex([1, 23], '\d+');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testDateISO8601()
    {
        $expected = [
            'value'             => '2010-01-17',
            'regex'             => '^([\\+-]?\\d{4}(?!\\d{2}\\b))((-?)((0[1-9]|1[0-2])(\\3([12]\\d|0[1-9]|3[01]))?|W([0-4]\\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\\d|[12]\\d{2}|3([0-5]\\d|6[1-6])))?)$',
            'pact:matcher:type' => 'regex',
        ];

        $actual = $this->matcher->dateISO8601('2010-01-17');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider dataProviderForTimeTest
     *
     * @throws Exception
     */
    public function testTimeISO8601($time)
    {
        $expected = [
            'value'             => $time,
            'regex'             => '^(T\\d\\d:\\d\\d(:\\d\\d)?(\\.\\d+)?([+-][0-2]\\d(?:|:?[0-5]\\d)|Z)?)$',
            'pact:matcher:type' => 'regex',
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
    public function testDateTimeISO8601($dateTime)
    {
        $expected = [
            'value'             => $dateTime,
            'regex'             => '^\\d{4}-[01]\\d-[0-3]\\dT[0-2]\\d:[0-5]\\d:[0-5]\\d([+-][0-2]\\d(?:|:?[0-5]\\d)|Z)?$',
            'pact:matcher:type' => 'regex',
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
    public function testDateTimeWithMillisISO8601($dateTime)
    {
        $expected = [
            'value'             => $dateTime,
            'regex'             => '^\\d{4}-[01]\\d-[0-3]\\dT[0-2]\\d:[0-5]\\d:[0-5]\\d\\.\\d{3}([+-][0-2]\\d(?:|:?[0-5]\\d)|Z)?$',
            'pact:matcher:type' => 'regex',
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
            'value'             => 'Mon, 31 Oct 2016 15:21:41 -0400',
            'regex'             => '^(Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s\\d{2}\\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\\s\\d{4}\\s\\d{2}:\\d{2}:\\d{2}\\s(\\+|-)\\d{4}$',
            'pact:matcher:type' => 'regex',
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

        $this->assertEquals('{"value":13,"pact:matcher:type":"type"}', $json);
    }

    /**
     * @throws Exception
     */
    public function testBoolean()
    {
        $json = \json_encode($this->matcher->boolean());

        $this->assertEquals('{"value":true,"pact:matcher:type":"type"}', $json);
    }

    /**
     * @throws Exception
     */
    public function testDecimal()
    {
        $json = \json_encode($this->matcher->decimal());

        $this->assertEquals('{"value":13.01,"pact:matcher:type":"type"}', $json);
    }

    public function testIntegerV3()
    {
        $expected = [
            'value' => 13,
            'pact:matcher:type' => 'integer',
        ];
        $actual = $this->matcher->integerV3(13);

        $this->assertEquals($expected, $actual);
    }

    public function testRandomIntegerV3()
    {
        $expected = [
            'pact:generator:type' => 'RandomInt',
            'pact:matcher:type' => 'integer',
        ];
        $actual = $this->matcher->integerV3();

        $this->assertEquals($expected, $actual);
    }

    public function testBooleanV3()
    {
        $expected = [
            'value' => true,
            'pact:matcher:type' => 'boolean',
        ];
        $actual = $this->matcher->booleanV3(true);

        $this->assertEquals($expected, $actual);
    }

    public function testRandomBooleanV3()
    {
        $expected = [
            'pact:generator:type' => 'RandomBoolean',
            'pact:matcher:type' => 'boolean',
        ];
        $actual = $this->matcher->booleanV3();

        $this->assertEquals($expected, $actual);
    }

    public function testDecimalV3()
    {
        $expected = [
            'value' => 13.01,
            'pact:matcher:type' => 'decimal',
        ];
        $actual = $this->matcher->decimalV3(13.01);

        $this->assertEquals($expected, $actual);
    }

    public function testRandomDecimalV3()
    {
        $expected = [
            'pact:generator:type' => 'RandomDecimal',
            'pact:matcher:type' => 'decimal',
        ];
        $actual = $this->matcher->decimalV3();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testHexadecimal()
    {
        $expected = [
            'value'             => '3F',
            'regex'             => '^[0-9a-fA-F]+$',
            'pact:matcher:type' => 'regex',
        ];
        $actual = $this->matcher->hexadecimal('3F');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testRandomHexadecimal()
    {
        $expected = [
            'regex'               => '^[0-9a-fA-F]+$',
            'pact:matcher:type'   => 'regex',
            'pact:generator:type' => 'RandomHexadecimal',
        ];
        $actual = $this->matcher->hexadecimal();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testUuid()
    {
        $expected = [
            'value'             => 'ce118b6e-d8e1-11e7-9296-cec278b6b50a',
            'regex'             => '^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$',
            'pact:matcher:type' => 'regex',
        ];
        $actual = $this->matcher->uuid('ce118b6e-d8e1-11e7-9296-cec278b6b50a');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testRandomUuid()
    {
        $expected = [
            'pact:generator:type' => 'Uuid',
            'regex'               => '^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$',
            'pact:matcher:type'   => 'regex',
        ];
        $actual = $this->matcher->uuid();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testIpv4Address()
    {
        $expected = [
            'value'             => '127.0.0.13',
            'regex'             => '^(\\d{1,3}\\.)+\\d{1,3}$',
            'pact:matcher:type' => 'regex',
        ];

        $this->assertEquals($expected, $this->matcher->ipv4Address());
    }

    /**
     * @throws Exception
     */
    public function testIpv6Address()
    {
        $expected = [
            'value'             => '::ffff:192.0.2.128',
            'regex'             => '^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$',
            'pact:matcher:type' => 'regex',
        ];

        $this->assertEquals($expected, $this->matcher->ipv6Address());
    }

    /**
     * @throws Exception
     */
    public function testEmail()
    {
        $expected = [
            'value'             => 'hello@pact.io',
            'regex'             => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$',
            'pact:matcher:type' => 'regex',
        ];
        $this->assertEquals($expected, $this->matcher->email());
    }

    /**
     * @throws Exception
     */
    public function testIpv4AddressV3()
    {
        $expected = $this->matcher->ipv4Address();
        $actual = $this->matcher->ipv4AddressV3('127.0.0.13');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testIpv6AddressV3()
    {
        $expected = $this->matcher->ipv6Address();
        $actual = $this->matcher->ipv6AddressV3('::ffff:192.0.2.128');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testEmailV3()
    {
        $expected = $this->matcher->email();
        $actual = $this->matcher->emailV3('hello@pact.io');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testRandomIpv4AddressV3()
    {
        $expected = [
            'regex'               => '^(\\d{1,3}\\.)+\\d{1,3}$',
            'pact:matcher:type'   => 'regex',
            'pact:generator:type' => 'Regex',
        ];
        $actual = $this->matcher->ipv4AddressV3();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testRandomIpv6AddressV3()
    {
        $expected = [
            'regex'               => '^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$',
            'pact:matcher:type'   => 'regex',
            'pact:generator:type' => 'Regex',
        ];
        $actual = $this->matcher->ipv6AddressV3();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testRandomEmailV3()
    {
        $expected = [
            'regex'               => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$',
            'pact:matcher:type'   => 'regex',
            'pact:generator:type' => 'Regex',
        ];
        $actual = $this->matcher->emailV3();
        $this->assertEquals($expected, $actual);
    }

    public function testNullValue()
    {
        $expected = [
            'pact:matcher:type' => 'null',
        ];
        $actual = $this->matcher->nullValue();
        $this->assertEquals($expected, $actual);
    }

    public function testDate()
    {
        $expected = [
            'value'             => '2022-11-21',
            'pact:matcher:type' => 'date',
            'format'            => 'yyyy-MM-dd',
        ];
        $actual = $this->matcher->date('yyyy-MM-dd', '2022-11-21');

        $this->assertEquals($expected, $actual);
    }

    public function testRandomDate()
    {
        $expected = [
            'pact:generator:type' => 'Date',
            'pact:matcher:type'   => 'date',
            'format'              => 'yyyy-MM-dd',
        ];
        $actual = $this->matcher->date();

        $this->assertEquals($expected, $actual);
    }

    public function testTime()
    {
        $expected = [
            'value'             => '21:45::31',
            'pact:matcher:type' => 'time',
            'format'            => 'HH:mm:ss',
        ];
        $actual = $this->matcher->time('HH:mm:ss', '21:45::31');

        $this->assertEquals($expected, $actual);
    }

    public function testRandomTime()
    {
        $expected = [
            'pact:generator:type' => 'Time',
            'pact:matcher:type'   => 'time',
            'format'              => 'HH:mm:ss',
        ];
        $actual = $this->matcher->time();

        $this->assertEquals($expected, $actual);
    }

    public function testDateTime()
    {
        $expected = [
            'value'             => '2015-08-06T16:53:10',
            'pact:matcher:type' => 'datetime',
            'format'            => "yyyy-MM-dd'T'HH:mm:ss",
        ];
        $actual = $this->matcher->datetime("yyyy-MM-dd'T'HH:mm:ss", '2015-08-06T16:53:10');

        $this->assertEquals($expected, $actual);
    }

    public function testRandomDateTime()
    {
        $expected = [
            'pact:generator:type' => 'DateTime',
            'pact:matcher:type'   => 'datetime',
            'format'              => "yyyy-MM-dd'T'HH:mm:ss",
        ];
        $actual = $this->matcher->datetime();

        $this->assertEquals($expected, $actual);
    }

    public function testString()
    {
        $expected = [
            'pact:matcher:type'   => 'type',
            'value'               => 'test string',
        ];
        $actual = $this->matcher->string('test string');

        $this->assertEquals($expected, $actual);
    }

    public function testRandomString()
    {
        $expected = [
            'pact:generator:type' => 'RandomString',
            'pact:matcher:type'   => 'type',
            'value'               => 'some string',
        ];
        $actual = $this->matcher->string();

        $this->assertEquals($expected, $actual);
    }

    public function testFromProviderState()
    {
        $expected = [
            'regex'               => Matcher::UUID_V4_FORMAT,
            'pact:matcher:type'   => 'regex',
            'value'               => 'f2392c53-6e55-48f7-8e08-18e4bf99c795',
            'pact:generator:type' => 'ProviderState',
            'expression'          => '${id}',
        ];
        $actual = $this->matcher->fromProviderState($this->matcher->uuid('f2392c53-6e55-48f7-8e08-18e4bf99c795'), '${id}');

        $this->assertEquals($expected, $actual);
    }

    public function testEqual()
    {
        $expected = [
            'pact:matcher:type' => 'equality',
            'value'             => 'test string',
        ];
        $actual = $this->matcher->equal('test string');

        $this->assertEquals($expected, $actual);
    }

    public function testIncludes()
    {
        $expected = [
            'pact:matcher:type' => 'include',
            'value'             => 'test string',
        ];
        $actual = $this->matcher->includes('test string');

        $this->assertEquals($expected, $actual);
    }

    public function testNumber()
    {
        $expected = [
            'value' => 13.01,
            'pact:matcher:type' => 'number',
        ];
        $actual = $this->matcher->number(13.01);

        $this->assertEquals($expected, $actual);
    }

    public function testRandomNumber()
    {
        $expected = [
            'pact:generator:type' => 'RandomInt',
            'pact:matcher:type' => 'number',
        ];
        $actual = $this->matcher->number();

        $this->assertEquals($expected, $actual);
    }

    public function testArrayContaining()
    {
        $expected = [
            'pact:matcher:type' => 'arrayContains',
            'variants'          => [
                'item 1',
                'item 2'
            ],
        ];
        $actual = $this->matcher->arrayContaining([
            'item 1',
            'item 2'
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testArrayContainingWithKeys()
    {
        $expected = [
            'pact:matcher:type' => 'arrayContains',
            'variants'          => [
                'item 1',
                'item 2'
            ],
        ];
        $actual = $this->matcher->arrayContaining([
            'key 1' => 'item 1',
            'key 2' => 'item 2'
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testNotEmpty()
    {
        $expected = [
            'value'             => 'not empty string',
            'pact:matcher:type' => 'notEmpty',
        ];
        $actual = $this->matcher->notEmpty('not empty string');

        $this->assertEquals($expected, $actual);
    }

    public function testSemver()
    {
        $expected = [
            'value'             => '1.2.3',
            'pact:matcher:type' => 'semver',
        ];
        $actual = $this->matcher->semver('1.2.3');

        $this->assertEquals($expected, $actual);
    }

    public function testInvalidStatusCode()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Status 'invalid' is not supported. Supported status are: info, success, redirect, clientError, serverError, nonError, error");
        $this->matcher->statusCode('invalid');
    }

    public function testValidStatusCode()
    {
        $expected = [
            'pact:generator:type' => 'RandomInt',
            'min'                 => 200,
            'max'                 => 299,
            'status'              => 'success',
            'pact:matcher:type'   => 'statusCode',
        ];
        $actual = $this->matcher->statusCode(HttpStatus::SUCCESS);

        $this->assertEquals($expected, $actual);
    }

    public function testValues()
    {
        $expected = [
            'pact:matcher:type' => 'values',
            'value'             => [
                'item 1',
                'item 2'
            ],
        ];
        $actual = $this->matcher->values([
            'item 1',
            'item 2'
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testValuesWithKeys()
    {
        $expected = [
            'pact:matcher:type' => 'values',
            'value'             => [
                'key 1' => 'item 1',
                'key 2' => 'item 2'
            ],
        ];
        $actual = $this->matcher->values([
            'key 1' => 'item 1',
            'key 2' => 'item 2'
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testContentType()
    {
        $expected = [
            'value'             => 'image/jpeg',
            'pact:matcher:type' => 'contentType',
        ];
        $actual = $this->matcher->contentType('image/jpeg');

        $this->assertEquals($expected, $actual);
    }

    public function testEachKey()
    {
        $values = [
            'page 1' => 'Hello',
            'page 2' => 'World',
        ];
        $rules = [
            $this->matcher->regex('page 3', '^page \d+$'),
        ];
        $expected = [
            'rules'             => $rules,
            'value'             => $values,
            'pact:matcher:type' => 'eachKey',
        ];
        $actual = $this->matcher->eachKey($values, $rules);

        $this->assertEquals($expected, $actual);
    }

    public function testEachValue()
    {
        $values = [
            'vehicle 1' => 'car',
            'vehicle 2' => 'bike',
            'vehicle 3' => 'motorbike'
        ];
        $rules = [
            $this->matcher->regex('car', 'car|bike|motorbike'),
        ];
        $expected = [
            'rules'             => $rules,
            'value'             => $values,
            'pact:matcher:type' => 'eachValue',
        ];
        $actual = $this->matcher->eachValue($values, $rules);

        $this->assertEquals($expected, $actual);
    }
}
