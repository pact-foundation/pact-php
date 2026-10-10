<?php

namespace PhpPactTest\FFI;

use PhpPact\FFI\Client;
use PhpPact\Log\Enum\LogLevel;
use PhpPact\Log\Exception\LoggerApplyException;
use PhpPact\Log\Exception\LoggerAttachSinkException;
use PhpPact\Log\Logger;
use PhpPact\Log\Model\File;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ClientLoggingTest extends TestCase
{
    private Client&MockObject $client;

    public function setUp(): void
    {
        Logger::tearDown();
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'loggerInit',
                'loggerAttachSink',
                'loggerApply',
                'getLevelFilterTrace',
                'getLevelFilterDebug',
                'getLevelFilterInfo',
                'getLevelFilterWarn',
                'getLevelFilterError',
                'getLevelFilterOff',
            ])
            ->getMock();
    }

    public function tearDown(): void
    {
        Logger::tearDown();
    }

    #[TestWith(['TRACE', 'getLevelFilterTrace'])]
    #[TestWith(['debug', 'getLevelFilterDebug'])]
    #[TestWith(['INFO', 'getLevelFilterInfo'])]
    #[TestWith(['WARN', 'getLevelFilterWarn'])]
    #[TestWith(['ERROR', 'getLevelFilterError'])]
    #[TestWith(['OFF', 'getLevelFilterOff'])]
    #[TestWith(['NONE', 'getLevelFilterOff'])]
    #[TestWith(['unknown', 'getLevelFilterInfo'])]
    public function testInitWithLogLevelUsesStdout(string $level, string $levelMethod): void
    {
        $this->client->expects($this->once())->method('loggerInit');
        $this->client->expects($this->once())->method($levelMethod)->willReturn(3);
        $this->client->expects($this->once())->method('loggerAttachSink')->with('stdout', 3)->willReturn(0);
        $this->client->expects($this->once())->method('loggerApply')->willReturn(0);

        $this->client->initWithLogLevel($level);
        $this->client->initWithLogLevel($level);
    }

    public function testInitWithLogLevelPreservesAppliedFileLogger(): void
    {
        $this->client->expects($this->once())->method('getLevelFilterDebug')->willReturn(4);
        $this->client->expects($this->once())->method('loggerAttachSink')->with('file pact.log', 4)->willReturn(0);
        $this->client->expects($this->once())->method('loggerApply')->willReturn(0);
        $logger = Logger::instance($this->client);
        $logger->attach(new File('pact.log', LogLevel::DEBUG));
        $logger->apply();

        $this->client->initWithLogLevel('DEBUG');
    }

    public function testInitWithLogLevelReportsSinkFailure(): void
    {
        $this->client->method('getLevelFilterDebug')->willReturn(4);
        $this->client->method('loggerAttachSink')->with('stdout', 4)->willReturn(-4);
        $this->client->expects($this->never())->method('loggerApply');
        $this->expectException(LoggerAttachSinkException::class);

        $this->client->initWithLogLevel('DEBUG');
    }

    public function testInitWithLogLevelReportsApplyFailure(): void
    {
        $this->client->method('loggerAttachSink')->willReturn(0);
        $this->client->method('loggerApply')->willReturn(-1);
        $this->expectException(LoggerApplyException::class);

        $this->client->initWithLogLevel('DEBUG');
    }

    public function testNativeDebugLogsDoNotFillStderr(): void
    {
        $code = <<<'PHP'
require $argv[1];
$client = new PhpPact\FFI\Client();
$client->initWithLogLevel('DEBUG');
$pact = $client->newPact('logging-consumer', 'logging-provider');
$interaction = $client->newInteraction($pact, 'logging');
for ($index = 0; $index < 64; $index++) {
    if (!$client->withHeaderV2($interaction, $client->getInteractionPartRequest(), 'Accept', 0, 'image/jpeg')) {
        throw new RuntimeException('Could not configure the logging interaction');
    }
}
$client->freePactHandle($pact);
PHP;
        $process = new Process(
            [PHP_BINARY, '-d', 'ffi.enable=true', '-r', $code, __DIR__ . '/../../../vendor/autoload.php'],
            null,
            ['PACT_DO_NOT_TRACK' => 'true'],
            null,
            10
        );
        $process->run();

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        $this->assertSame('', $process->getErrorOutput());
        $this->assertStringContainsString('DEBUG', $process->getOutput());
        $this->assertGreaterThan(4096, strlen($process->getOutput()));
    }
}
