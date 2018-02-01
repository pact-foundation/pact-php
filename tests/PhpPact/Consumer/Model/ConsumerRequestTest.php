<?php

namespace PhpPact\Consumer\Model;

use PhpPact\Consumer\Matcher\LikeMatcher;
use PhpPact\Consumer\Matcher\RegexMatcher;
use PHPUnit\Framework\TestCase;

class ConsumerRequestTest extends TestCase
{
    public function testSerializing()
    {
        $model = new ConsumerRequest();
        $model
            ->setMethod('PUT')
            ->setPath('/somepath')
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'age'            => new LikeMatcher(12, 0, 100),
                'currentCity'    => 'Austin',
                'previousCities' => new LikeMatcher([
                    'Dallas',
                    'Houston',
                    'San Antonio',
                    'Pittsburgh'
                ]),
                'dates' => new RegexMatcher([
                    '01/11/2017',
                    '04/17/2012',
                    '08/06/1987'
                ], '^(0[1-9]|1[012])[/](0[1-9]|[12][0-9]|3[01])[/](19|20)\d\d$')
            ]);

        $data = \json_decode(\json_encode($model->jsonSerialize()), true);

        // String
        $this->assertEquals(12, $data['body']['age']);
        $this->assertEquals('Austin', $data['body']['currentCity']);

        // Int Like
        $this->assertArrayHasKey('$.body.age', $data['matchingRules']);
        $this->assertEquals(12, $data['body']['age']);
        $this->assertEquals('type', $data['matchingRules']['$.body.age']['match']);
        $this->assertEquals(0, $data['matchingRules']['$.body.age']['min']);
        $this->assertEquals(100, $data['matchingRules']['$.body.age']['max']);

        // Array Like
        $this->assertArrayHasKey('$.body.previousCities[*]', $data['matchingRules']);
        $this->assertEquals('type', $data['matchingRules']['$.body.previousCities[*]']['match']);
        $this->assertTrue(\in_array('Dallas', $data['body']['previousCities']));
        $this->assertTrue(\in_array('Houston', $data['body']['previousCities']));
        $this->assertTrue(\in_array('San Antonio', $data['body']['previousCities']));
        $this->assertTrue(\in_array('Pittsburgh', $data['body']['previousCities']));

        // Array Regex
        $this->assertArrayHasKey('$.body.dates[*]', $data['matchingRules']);
        $this->assertEquals('regex', $data['matchingRules']['$.body.dates[*]']['match']);
        $this->assertEquals('^(0[1-9]|1[012])[/](0[1-9]|[12][0-9]|3[01])[/](19|20)\d\d$', $data['matchingRules']['$.body.dates[*]']['regex']);
        $this->assertTrue(\in_array('01/11/2017', $data['body']['dates']));
        $this->assertTrue(\in_array('04/17/2012', $data['body']['dates']));
        $this->assertTrue(\in_array('08/06/1987', $data['body']['dates']));
    }

    public function testAddMatchingRule()
    {
        $pattern = '$.body.something[*]';

        $model = new ConsumerRequest();
        $model
            ->setMethod('PUT')
            ->setPath('/somepath')
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'something' => [
                    1,
                    2,
                    3
                ]
            ])
            ->addMatchingRule($pattern, new LikeMatcher());

        $data = \json_decode(\json_encode($model->jsonSerialize()), true);

        $this->assertArrayHasKey($pattern, $data['matchingRules']);
        $this->assertEquals('type', $data['matchingRules'][$pattern]['match']);
    }
}
