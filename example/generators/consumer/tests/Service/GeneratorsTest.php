<?php

namespace GeneratorsConsumer\Tests\Service;

use DateTime;
use GeneratorsConsumer\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Generators\Uuid;
use PhpPact\Consumer\Matcher\HttpStatus;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
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

    public function testGetGenerators(): void
    {
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/generators')
            ->addHeader('Accept', 'application/json')
            ->setBody([
                'id' => $this->matcher->fromProviderState($this->matcher->integerV3(), '${id}')
            ]);

        $response = new ProviderResponse();
        $response
            ->setStatus($this->matcher->statusCode(HttpStatus::CLIENT_ERROR))
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'regex' => $this->matcher->regex(null, $regexWithoutAnchors = '\d+ (miles|kilometers)'),
                'boolean_v3' => $this->matcher->booleanV3(null),
                'integer_v3' => $this->matcher->integerV3(null),
                'decimal_v3' => $this->matcher->decimalV3(null),
                'hexadecimal' => $this->matcher->hexadecimal(null),
                'uuid' => $this->matcher->uuid(null),
                'date' => $this->matcher->date('yyyy-MM-dd', null),
                'time' => $this->matcher->time('HH:mm:ss', null),
                'datetime' => $this->matcher->datetime("yyyy-MM-dd'T'HH:mm:ss", null),
                'string' => $this->matcher->string(null),
                'number' => $this->matcher->number(null),
                'url' => $this->matcher->url('http://localhost/users/1234/posts/latest', '.*(\\/users\\/\\d+\\/posts\\/latest)$'),
                'notEmpty' => $this->matcher->notEmpty('text')->withGenerator(new RandomString()),
                'equality' => $this->matcher->equal('Hello World!')->withGenerator(new RandomString()),
                'like' => $this->matcher->like('6057401b-c539-4948-971a-24b702d79882')->withGenerator(new Uuid()),
                'boolean' => $this->matcher->boolean(null),
                'integer' => $this->matcher->integer(null),
                'decimal' => $this->matcher->decimal(null),
                'semver' => $this->matcher->semver(null),
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
        $this->assertMatchesRegularExpression('/^' . $regexWithoutAnchors . '$/', $body['regex']);
        $this->assertIsBool($body['boolean_v3']);
        $this->assertIsInt($body['integer_v3']);
        $this->assertIsFloat($body['decimal_v3'] + 0);
        $this->assertMatchesRegularExpression('/' . Matcher::HEX_FORMAT . '/', $body['hexadecimal']);
        $this->assertMatchesRegularExpression('/' . Matcher::UUID_V4_FORMAT . '/', $body['uuid']);
        $this->assertTrue($this->validateDateTime($body['date'], 'Y-m-d'));
        $this->assertTrue($this->validateDateTime($body['time'], 'H:i:s'));
        $this->assertTrue($this->validateDateTime($body['datetime'], "Y-m-d\TH:i:s"));
        $this->assertIsString($body['string']);
        $this->assertNotSame('', $body['string']);
        $this->assertIsNumeric($body['number']);
        $this->assertNotSame('http://localhost/users/1234/posts/latest', $body['url']);
        $this->assertMatchesRegularExpression('/.*(\\/users\\/\\d+\\/posts\\/latest)$/', $body['url']);
        $this->assertIsString($body['notEmpty']);
        $this->assertNotEmpty($body['notEmpty']);
        $this->assertIsString($body['equality']);
        $this->assertNotSame('Hello World!', $body['equality']);
        $this->assertIsString($body['like']);
        $this->assertMatchesRegularExpression('/' . Matcher::UUID_V4_FORMAT . '/', $body['like']);
        $this->assertIsBool($body['boolean']);
        $this->assertIsInt($body['integer']);
        $this->assertIsFloat($body['decimal'] + 0);
        $this->assertMatchesRegularExpression('/\d+\.\d+\.\d+/', $body['semver']);
        $this->assertSame(222, $body['requestId']);
    }

    private function validateDateTime(string $datetime, string $format): bool
    {
        $value = DateTime::createFromFormat($format, $datetime);

        return $value && $value->format($format) === $datetime;
    }
}
