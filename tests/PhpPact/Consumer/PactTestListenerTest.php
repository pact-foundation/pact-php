<?php

namespace PhpPact\Consumer;

use GuzzleHttp\Exception\ConnectException;
use Mockery;
use PhpPact\Consumer\Service\MockServerHttpService;
use PhpPact\Core\Http\GuzzleClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;

class PactTestListenerTest extends TestCase
{
    public function testStartAndEndTestSuite()
    {
        $name     = 'FakeTestSuite';
        $listener = new PactTestListener([$name]);

        $suite = Mockery::mock(TestSuite::class);
        $suite->shouldReceive('getName')->once()->andReturn($name);

        // Verify that the start, starts the server as expected.
        /* @var TestSuite $suite */
        $listener->startTestSuite($suite);
        $service = new MockServerHttpService(new GuzzleClient(), new MockServerEnvConfig());
        $status  = $service->healthCheck();
        $this->assertTrue($status);

        // Verify that the end stop the server as expected.
        $listener->endTestSuite($suite);
        $this->expectException(ConnectException::class);
        $service->healthCheck();
    }
}
