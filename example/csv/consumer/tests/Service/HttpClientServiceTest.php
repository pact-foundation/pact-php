<?php

namespace CsvConsumer\Tests\Service;

use CsvConsumer\Service\HttpClientService;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Plugins\Csv\Factory\CsvInteractionDriverFactory;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class HttpClientServiceTest extends TestCase
{
    public function testGetCsvFile(): void
    {
        $matcher = new Matcher(plugin: true);

        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/report.csv')
            ->addHeader('Accept', 'text/csv')
        ;

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->setBody(new Text(
                json_encode([
                    'csvHeaders' => false,
                    'column:1' => $matcher->like('Name'),
                    'column:2' => $matcher->number(100),
                    'column:3' => $matcher->datetime('yyyy-MM-dd', '2000-01-01'),
                ]),
                'text/csv'
            ))
        ;

        $config = new MockServerConfig();
        $config
            ->setConsumer('csvConsumer')
            ->setProvider('csvProvider')
            ->setPactSpecificationVersion('4.0.0')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config, new CsvInteractionDriverFactory(InteractionPart::RESPONSE));
        $builder
            ->given('report.csv file exist')
            ->uponReceiving('request for a report.csv')
            ->with($request)
            ->willRespondWith($response)
        ;

        $service = new HttpClientService($config->getBaseUri());
        $columns = $service->getReport();

        $this->assertTrue($builder->verify());
        $this->assertCount(3, $columns);
        $this->assertSame(['Name', '100', '2000-01-01'], $columns);
    }
}
