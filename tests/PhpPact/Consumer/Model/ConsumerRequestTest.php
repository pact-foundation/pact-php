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

        // Age
        $this->assertEquals(12, $data['body']['age']['contents']);
        $this->assertEquals('Pact::SomethingLike', $data['body']['age']['json_class']);

        // Current City
        $this->assertEquals('Austin', $data['body']['currentCity']);

        // Previous City
        $this->assertEquals([
            'Dallas',
            'Houston',
            'San Antonio',
            'Pittsburgh'
        ], $data['body']['previousCities']['contents']);
        $this->assertEquals('Pact::ArrayLike', $data['body']['previousCities']['json_class']);

        // Dates
        $this->assertEquals('Pact::Term', $data['body']['dates']['contents'][0]['json_class']);
        $this->assertEquals('01/11/2017', $data['body']['dates']['contents'][0]['data']['generate']);
        $this->assertEquals('Regexp', $data['body']['dates']['contents'][0]['data']['matcher']['json_class']);
        $this->assertEquals(0, $data['body']['dates']['contents'][0]['data']['matcher']['o']);
        $this->assertEquals('^(0[1-9]|1[012])[/](0[1-9]|[12][0-9]|3[01])[/](19|20)\d\d$', $data['body']['dates']['contents'][0]['data']['matcher']['s']);

        $this->assertEquals('Pact::Term', $data['body']['dates']['contents'][1]['json_class']);
        $this->assertEquals('04/17/2012', $data['body']['dates']['contents'][1]['data']['generate']);
        $this->assertEquals('Regexp', $data['body']['dates']['contents'][1]['data']['matcher']['json_class']);
        $this->assertEquals(0, $data['body']['dates']['contents'][1]['data']['matcher']['o']);
        $this->assertEquals('^(0[1-9]|1[012])[/](0[1-9]|[12][0-9]|3[01])[/](19|20)\d\d$', $data['body']['dates']['contents'][1]['data']['matcher']['s']);

        $this->assertEquals('Pact::Term', $data['body']['dates']['contents'][2]['json_class']);
        $this->assertEquals('08/06/1987', $data['body']['dates']['contents'][2]['data']['generate']);
        $this->assertEquals('Regexp', $data['body']['dates']['contents'][2]['data']['matcher']['json_class']);
        $this->assertEquals(0, $data['body']['dates']['contents'][2]['data']['matcher']['o']);
        $this->assertEquals('^(0[1-9]|1[012])[/](0[1-9]|[12][0-9]|3[01])[/](19|20)\d\d$', $data['body']['dates']['contents'][2]['data']['matcher']['s']);
    }
}
