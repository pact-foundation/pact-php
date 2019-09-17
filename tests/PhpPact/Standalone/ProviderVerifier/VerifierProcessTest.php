<?php

namespace PhpPactTest\Standalone\ProviderVerifier;

use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\ProviderVerifier\ProcessRunnerFactory;
use PhpPact\Standalone\ProviderVerifier\VerifierProcess;
use PhpPact\Standalone\Runner\ProcessRunner;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class VerifierProcessTest extends TestCase
{
    public function testRun()
    {
        $verifier  = 'foo';
        $arguments = ['foo' => 'bar'];

        $scripts = $this->createMock(Scripts::class);
        $scripts->expects($this->once())
            ->method('getProviderVerifier')
            ->will($this->returnValue($verifier));

        $logger = $this->createMock(LoggerInterface::class);

        $processRunner = $this->createMock(ProcessRunner::class);

        $installManager = $this->createMock(InstallManager::class);
        $installManager->expects($this->once())
            ->method('install')
            ->will($this->returnValue($scripts));

        $processRunnerFactory = $this->createMock(ProcessRunnerFactory::class);
        $processRunnerFactory->expects($this->once())
            ->method('createRunner')
            ->with($this->equalTo($verifier), $this->equalTo($arguments), $this->equalTo($logger))
            ->will($this->returnValue($processRunner));

        $process = new VerifierProcess($installManager, $processRunnerFactory);
        $process->setLogger($logger);
        $process->run($arguments, 42, 23);
    }

    public function testRunWithDefaultLogger()
    {
        $verifier  = 'foo';
        $arguments = ['foo' => 'bar'];

        $scripts = $this->createMock(Scripts::class);
        $scripts->expects($this->once())
            ->method('getProviderVerifier')
            ->will($this->returnValue($verifier));

        $processRunner = $this->createMock(ProcessRunner::class);

        $installManager = $this->createMock(InstallManager::class);
        $installManager->expects($this->once())
            ->method('install')
            ->will($this->returnValue($scripts));

        $processRunnerFactory = $this->createMock(ProcessRunnerFactory::class);
        $processRunnerFactory->expects($this->once())
            ->method('createRunner')
            ->with($this->equalTo($verifier), $this->equalTo($arguments))
            ->will($this->returnValue($processRunner));

        $process = new VerifierProcess($installManager, $processRunnerFactory);
        $process->run($arguments, 42, 23);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage foo
     */
    public function testRunForwardsException()
    {
        $verifier  = 'foo';
        $arguments = ['foo' => 'bar'];

        $expectedException = new \RuntimeException('foo');

        $scripts = $this->createMock(Scripts::class);
        $scripts->expects($this->once())
            ->method('getProviderVerifier')
            ->will($this->returnValue($verifier));

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

        $installManager = $this->createMock(InstallManager::class);
        $installManager->expects($this->once())
            ->method('install')
            ->will($this->returnValue($scripts));

        $processRunnerFactory = $this->createMock(ProcessRunnerFactory::class);
        $processRunnerFactory->expects($this->once())
            ->method('createRunner')
            ->with($this->equalTo($verifier), $this->equalTo($arguments))
            ->will($this->returnValue($processRunner));

        $process = new VerifierProcess($installManager, $processRunnerFactory);
        $process->run($arguments, 42, 23);
    }
}
