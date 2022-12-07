<?php

namespace PhpPactTest\Standalone\ProviderVerifier;

use PhpPact\Standalone\ProviderVerifier\ProcessRunnerFactory;
use PhpPact\Standalone\ProviderVerifier\VerifierProcess;
use PhpPact\Standalone\Runner\ProcessRunner;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class VerifierProcessTest extends TestCase
{
    public function testRun()
    {
        $arguments = ['foo' => 'bar'];

        $logger = $this->createMock(LoggerInterface::class);

        $processRunner = $this->createMock(ProcessRunner::class);

        $processRunnerFactory = $this->createMock(ProcessRunnerFactory::class);
        $processRunnerFactory->expects($this->once())
            ->method('createRunner')
            ->with($this->equalTo($arguments), $this->equalTo($logger))
            ->will($this->returnValue($processRunner));

        $process = new VerifierProcess($processRunnerFactory);
        $process->setLogger($logger);
        $process->run($arguments, 42, 23);
    }

    public function testRunWithDefaultLogger()
    {
        $arguments = ['foo' => 'bar'];

        $processRunner = $this->createMock(ProcessRunner::class);

        $processRunnerFactory = $this->createMock(ProcessRunnerFactory::class);
        $processRunnerFactory->expects($this->once())
            ->method('createRunner')
            ->with($this->equalTo($arguments))
            ->will($this->returnValue($processRunner));

        $process = new VerifierProcess($processRunnerFactory);
        $process->run($arguments, 42, 23);
    }

    public function testRunForwardsException()
    {
        $this->expectExceptionMessage('foo');
        $this->expectException(\RuntimeException::class);

        $arguments = ['foo' => 'bar'];

        $expectedException = new \RuntimeException('foo');

        $processRunner = $this->createMock(ProcessRunner::class);
        $processRunner->expects($this->once())
            ->method('runBlocking')
            ->will(
                $this->returnCallback(
                    function () use ($expectedException) {
                        throw $expectedException;
                    }
                )
            );

        $processRunnerFactory = $this->createMock(ProcessRunnerFactory::class);
        $processRunnerFactory->expects($this->once())
            ->method('createRunner')
            ->with($this->equalTo($arguments))
            ->will($this->returnValue($processRunner));

        $process = new VerifierProcess($processRunnerFactory);
        $process->run($arguments, 42, 23);
    }
}
