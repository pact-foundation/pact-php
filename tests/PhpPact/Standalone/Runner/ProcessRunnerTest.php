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
        //$this->assertEquals($p->getStderr(), null, 'Expect no std err');

        // try an app that does not exists
        $p = new ProcessRunner('failedApp', []);

        try {
            $p->runBlocking();
        } catch (\Exception $e) {
        }

        $exitCode = $p->getExitCode();
        $this->assertNotEquals($exitCode, 0, 'Expect the exit code to be non-zero: ' . $exitCode);
        //$this->assertEquals($p->getOutput(), null, 'Expect no output');
        print "\n*****************- \n" . \print_r($p->getStderr(), true) . "\n***************** \n";

        $this->assertTrue((\stripos($p->getStderr(), $expectedErr) !== false), "Expect '{$expectedErr}' to be in the stderr");
    }
}
