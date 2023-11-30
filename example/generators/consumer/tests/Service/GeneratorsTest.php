<?php

namespace GeneratorsConsumer\Tests\Service;

use DateTime;
use GeneratorsConsumer\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\HttpStatus;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class GeneratorsTest extends TestCase
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
            ->setPath('/generators')
            ->addHeader('Accept', 'application/json')
            ->setBody([
                'id' => $this->matcher->fromProviderState($this->matcher->integer(), '${id}')
            ]);

        $response = new ProviderResponse();
        $response
            ->setStatus($this->matcher->statusCode(HttpStatus::CLIENT_ERROR))
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'regex' => $this->matcher->regex(null, $regexWithoutAnchors = '\d+ (miles|kilometers)'),
                'boolean' => $this->matcher->booleanV3(null),
                'integer' => $this->matcher->integerV3(null),
                'decimal' => $this->matcher->decimalV3(null),
                'hexadecimal' => $this->matcher->hexadecimal(null),
                'uuid' => $this->matcher->uuid(null),
                'date' => $this->matcher->date('yyyy-MM-dd', null),
                'time' => $this->matcher->time('HH:mm:ss', null),
                'datetime' => $this->matcher->datetime("yyyy-MM-dd'T'HH:mm:ss", null),
                'string' => $this->matcher->string(null),
                'number' => $this->matcher->number(null),
                'requestId' => 222,
            ]);

        $config = new MockServerConfig();
        $config
            ->setConsumer('generatorsConsumer')
            ->setProvider('generatorsProvider')
            ->setPactDir(__DIR__.'/../../../pacts')
            ->setPactSpecificationVersion('4.0.0');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->given('Get Generators')
            ->uponReceiving('A get request to /generators')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpClientService($config->getBaseUri());
        $response = $service->sendRequest();
        $verifyResult = $builder->verify();

        $statusCode = $response->getStatusCode();
        $body = \json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertTrue($verifyResult);
        $this->assertThat(
            $statusCode,
            $this->logicalAnd(
                $this->greaterThanOrEqual(400),
                $this->lessThanOrEqual(499)
            )
        );
        $this->assertRegExp('/^' . $regexWithoutAnchors . '$/', $body['regex']);
        $this->assertIsBool($body['boolean']);
        $this->assertIsInt($body['integer']);
        $this->assertIsFloat($body['decimal'] + 0);
        $this->assertRegExp('/' . Matcher::HEX_FORMAT . '/', $body['hexadecimal']);
        $this->assertRegExp('/' . Matcher::UUID_V4_FORMAT . '/', $body['uuid']);
        $this->assertTrue($this->validateDateTime($body['date'], 'Y-m-d'));
        $this->assertTrue($this->validateDateTime($body['time'], 'H:i:s'));
        $this->assertTrue($this->validateDateTime($body['datetime'], "Y-m-d\TH:i:s"));
        $this->assertIsString($body['string']);
        $this->assertIsNumeric($body['number']);
        $this->assertSame(222, $body['requestId']);
    }

    private function validateDateTime(string $datetime, string $format): bool
    {
        $value = DateTime::createFromFormat($format, $datetime);

        return $value && $value->format($format) === $datetime;
    }
}
