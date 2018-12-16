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
            $expectedErr    = 'failedApp';
        } else {
            $p              = new ProcessRunner('dir', []);
            $expectedOutput = 'pact';
            $expectedErr    = 'failedApp';
        }

        $p->runBlocking();
        $exitCode = $p->getExitCode();

        $this->assertEquals($exitCode, 0, 'Expect the exit code to be 0');
        $this->assertTrue((\stripos($p->getOutput(), $expectedOutput) !== false), "Expect '{$expectedOutput}' to be in the output");

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
        $this->assertTrue((\stripos($p->getStderr(), $expectedErr) !== false), "Expect '{$expectedErr}' to be in the stderr");
    }
}
