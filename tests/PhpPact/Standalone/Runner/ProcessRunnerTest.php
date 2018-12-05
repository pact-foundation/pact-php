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
        print "\n***************** \n" . \print_r($p->getOutput(), true) . "\n***************** \n";
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
        $this->assertTrue((\stripos($p->getStderr(), 'failedApp') !== false), "Expect 'failedApp' to be in the stderr");
    }
}
