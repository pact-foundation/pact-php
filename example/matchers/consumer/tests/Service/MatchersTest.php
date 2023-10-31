<?php

namespace MatchersConsumer\Tests\Service;

use MatchersConsumer\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class MatchersTest extends TestCase
{
    private Matcher $matcher;

    public function setUp(): void
    {
        $this->matcher = new Matcher();
    }

    public function testGetMatchers()
    {
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath($this->matcher->regex('/matchers', '^\/matchers$'))
            ->setQuery([
                'ignore' => 'statusCode',
            ])
            ->addHeader('Accept', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'like' => $this->matcher->like(['key' => 'value']),
                'likeNull' => $this->matcher->like(null),
                'eachLike' => $this->matcher->eachLike('item'),
                'atLeastLike' => $this->matcher->atLeastLike(1, 5),
                'atMostLike' => $this->matcher->atMostLike(1, 3),
                'constrainedArrayLike' => $this->matcher->constrainedArrayLike('item', 2, 4),
                'regex' => $this->matcher->regex('500 miles', '^\d+ (miles|kilometers)$'),
                'dateISO8601' => $this->matcher->dateISO8601(),
                'timeISO8601' => $this->matcher->timeISO8601(),
                'dateTimeISO8601' => $this->matcher->dateTimeISO8601(),
                'dateTimeWithMillisISO8601' => $this->matcher->dateTimeWithMillisISO8601(),
                'timestampRFC3339' => $this->matcher->timestampRFC3339(),
                'likeBool' => $this->matcher->boolean(),
                'likeInt' => $this->matcher->integer(),
                'likeDecimal' => $this->matcher->decimal(),
                'boolean' => $this->matcher->booleanV3(false),
                'integer' => $this->matcher->integerV3(9),
                'decimal' => $this->matcher->decimalV3(79.01),
                'hexadecimal' => $this->matcher->hexadecimal('F7A16'),
                'uuid' => $this->matcher->uuid('52c9585e-f345-4964-aa28-a45c64b2b2eb'),
                'ipv4Address' => $this->matcher->ipv4Address(),
                'ipv6Address' => $this->matcher->ipv6Address(),
                'email' => $this->matcher->email(),
                'nullValue' => $this->matcher->nullValue(),
                'date' => $this->matcher->date('yyyy-MM-dd', '2015-05-16'),
                'time' => $this->matcher->time('HH:mm::ss', '23:59::58'),
                'datetime' => $this->matcher->datetime("YYYY-mm-DD'T'HH:mm:ss", '2000-10-31T01:30:00'),
                'likeString' => $this->matcher->string('some string'),
                'equal' => $this->matcher->equal('exact this value'),
                'includes' => $this->matcher->includes('lazy dog'),
                'number' => $this->matcher->number(123),
                'arrayContaining' => $this->matcher->arrayContaining([
                    'text' => $this->matcher->string('some text'),
                    'number' => $this->matcher->number(111),
                    'uuid' => $this->matcher->uuid('2fbd41cc-4bbc-44ea-a419-67f767691407'),
                ]),
                'notEmpty' => $this->matcher->notEmpty(['1','2','3']),
                'semver' => $this->matcher->semver('10.0.0-alpha4'),
                'values' => $this->matcher->values([
                    'a' => 'a',
                    'b' => 'bb',
                    'c' => 'ccc',
                ]),
                'contentType' => $this->matcher->contentType('text/html'),
            ]);

        $config = new MockServerConfig();
        $config
            ->setConsumer('matchersConsumer')
            ->setProvider('matchersProvider')
            ->setPactDir(__DIR__.'/../../../pacts')
            ->setPactSpecificationVersion('4.0.0');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->given('Get Matchers')
            ->uponReceiving('A get request to /matchers')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpClientService($config->getBaseUri());
        $matchersResult = $service->getMatchers();
        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
        $this->assertEquals([
            'like' => ['key' => 'value'],
            'likeNull' => null,
            'eachLike' => ['item'],
            'atLeastLike' => [1, 1, 1, 1, 1],
            'atMostLike' => [1],
            'constrainedArrayLike' => ['item', 'item'],
            'regex' => '500 miles',
            'dateISO8601' => '2013-02-01',
            'timeISO8601' => 'T22:44:30.652Z',
            'dateTimeISO8601' => '2015-08-06T16:53:10+01:00',
            'dateTimeWithMillisISO8601' => '2015-08-06T16:53:10.123+01:00',
            'timestampRFC3339' => 'Mon, 31 Oct 2016 15:21:41 -0400',
            'likeBool' => true,
            'likeInt' => 13,
            'likeDecimal' => 13.01,
            'boolean' => false,
            'integer' => 9,
            'decimal' => 79.01,
            'hexadecimal' => 'F7A16',
            'uuid' => '52c9585e-f345-4964-aa28-a45c64b2b2eb',
            'ipv4Address' => '127.0.0.13',
            'ipv6Address' => '::ffff:192.0.2.128',
            'email' => 'hello@pact.io',
            'nullValue' => null,
            'date' => '2015-05-16',
            'time' => '23:59::58',
            'datetime' => '2000-10-31T01:30:00',
            'likeString' => 'some string',
            'equal' => 'exact this value',
            'includes' => 'lazy dog',
            'number' => 123,
            'arrayContaining' => [
                'some text',
                111,
                '2fbd41cc-4bbc-44ea-a419-67f767691407',
            ],
            'notEmpty' => ['1', '2', '3'],
            'semver' => '10.0.0-alpha4',
            'values' => [
                'a',
                'bb',
                'ccc',
            ],
            'contentType' => 'text/html',
        ], $matchersResult);
    }
}
