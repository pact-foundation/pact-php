<?php

namespace ServerSentEventsConsumer\Tests\Service;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Plugins\Sse\Factory\SseInteractionDriverFactory;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;
use HosmelQ\SSE\Client as SseClient;

class HttpClientServiceTest extends TestCase
{
    public function testGetEvents(): void
    {
        $matcher = new Matcher(plugin: true);

        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/events')
            ->addHeader('Accept', 'text/event-stream')
        ;

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->setBody(new Text(
                json_encode([
                    'id' => $matcher->number(100),
                    // NOTE: Must use integerV3() instead of integer() because in SSE all values
                    // are strings (e.g., 'data:100'). integer() returns a Type matcher which only
                    // checks that both values are the same type (string), so 'aaa' would match '100'.
                    // integerV3() returns an Integer matcher that properly validates the value is numeric.
                    'retry' => $matcher->integerV3(100),
                    'event' => $matcher->like('count'),
                    'data' => $matcher->like("simple text\nother text"),
                    'data[count]' => $matcher->number(100),
                    'data[time]' => $matcher->datetime('yyyy-MM-dd', '2000-01-01'),
                    'data[user]' => $matcher->term('id: 123, name: Bob', 'id: \d+, name: \w+'),
                ]),
                'text/event-stream'
            ))
            ->setHeaders([
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ])
        ;

        $config = new MockServerConfig();
        $config
            ->setConsumer('sseConsumer')
            ->setProvider('sseProvider')
            ->setPactSpecificationVersion('4.0.0')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config, new SseInteractionDriverFactory(InteractionPart::RESPONSE));
        $builder
            ->given('events are available')
            ->uponReceiving('request for events')
            ->with($request)
            ->willRespondWith($response)
        ;

        $sseClient = new SseClient();
        $eventSource = $sseClient->get($config->getBaseUri() . '/events');
        $events = iterator_to_array($eventSource->events());

        $expectedEvents = [
            ['data' => "simple text\nother text", 'id' => '100', 'retry' => 100],
            ['data' => '100', 'id' => '100', 'retry' => null],
            ['data' => '2000-01-01', 'id' => '100', 'retry' => null],
            ['data' => 'id: 123, name: Bob', 'id' => '100', 'retry' => null],
        ];

        $this->assertCount(count($expectedEvents), $events);
        foreach ($events as $index => $event) {
            $this->assertSame($expectedEvents[$index]['data'], $event->data);
            $this->assertSame($expectedEvents[$index]['id'], $event->id);
            $this->assertSame($expectedEvents[$index]['retry'], $event->retry);
        }

        $this->assertTrue($builder->verify());
    }
}
