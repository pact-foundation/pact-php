<?php

namespace PhpPact\Standalone\Runner;

use PHPUnit\Framework\TestCase;

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
            $p              = new ProcessRunner('dir', []);
            $expectedOutput = 'pact';
        }

        $p->runBlocking();
        $exitCode = $p->getExitCode();

        $this->assertEquals($exitCode, 0, 'Expect the exit code to be 0');
        $this->assertTrue((\stripos($p->getOutput(), $expectedOutput) !== false), "Expect '{$expectedOutput}' to be in the output");
        $this->assertEquals($p->getStderr(), null, 'Expect a null stderr');

        // try an app that does not exists
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $p              = new ProcessRunner('failedApp', []);
            $expectedErr    = 'failedApp';
        } else {
            $p              = new ProcessRunner('dir', ['failed.xx']);
            $expectedErr    = 'failed.xx';
        }

        try {
            $p->runBlocking();
        } catch (\Exception $e) {
        }

        $exitCode = $p->getExitCode();

        $this->assertNotEquals($exitCode, 0, 'Expect the exit code to be non-zero: ' . $exitCode);
        $this->assertEquals($p->getOutput(), null, 'Expect a null output');
        $this->assertTrue((\stripos($p->getStderr(), $expectedErr) !== false), "Expect '{$expectedErr}' to be in the stderr");
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
