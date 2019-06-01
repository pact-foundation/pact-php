<?php

namespace PhpPact\Standalone\Runner;

use PHPUnit\Framework\TestCase;

use Amp\ByteStream;
use Amp\Loop;
use Amp\Process\Process;

class ProcessRunnerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testBlockingProcess()
    {
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $p              = new ProcessRunner('ls', ['-alt']);
            $expectedOutput = 'total';
        } else {
            $p              = new ProcessRunner('cmd /c dir', []);
            $expectedOutput = 'pact';
        }

        $p->runBlocking();
        $exitCode = $p->getExitCode();

        $this->assertEquals($exitCode, 0, 'Expect the exit code to be 0');
        $this->assertContains($expectedOutput, $p->getOutput(), "Expect '{$expectedOutput}' to be in the output");
        $this->assertEquals(null, $p->getStderr(), 'Expect a null stderr');

        // try an app that does not exists
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $p              = new ProcessRunner('failedApp', []);
            $expectedErr    = 'failedApp';
        } else {
            $p              = new ProcessRunner('cmd /c echo myError 1>&2 && exit 42', []);
            $expectedErr    = 'myError';
        }

        try {
            $p->runBlocking();
        } catch (\Exception $e) {
        }

        $exitCode = $p->getExitCode();

        $this->assertNotEquals($exitCode, 0, 'Expect the exit code to be non-zero: ' . $exitCode);
        $this->assertContains($expectedErr, $p->getStderr(), "Expect '{$expectedErr}' to be in the stderr");
        $this->assertEquals(null, $p->getOutput(), 'Expect a null stdout');
    }

    /**
     * @throws \Exception
     */
    public function testProcessRunnerShouldReturnCompleteOutput()
    {
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $cmd = __DIR__ . \DIRECTORY_SEPARATOR . 'verifier.sh';
        } else {
            $cmd = 'cmd /c' . __DIR__ . \DIRECTORY_SEPARATOR . 'verifier.bat';
        }

        $p              = new ProcessRunner($cmd, []);
        $expectedOutput = 'third line';
        $expectedErr    = 'fourth line';

        try {
            $p->runBlocking();
        } catch (\Exception $e) {
        }

        $this->assertTrue((\stripos($p->getOutput(), $expectedOutput) !== false), "Expect '{$expectedOutput}' to be in the output:");
        $this->assertTrue((\stripos($p->getStderr(), $expectedErr) !== false), "Expect '{$expectedErr}' to be in the stderr");
    }
}
