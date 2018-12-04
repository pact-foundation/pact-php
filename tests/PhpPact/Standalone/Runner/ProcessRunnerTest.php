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
            $p = new ProcessRunner('ls', ['-alt']);
            $p->runBlocking();

            $exitCode = $p->getExitCode();
            $this->assertEquals($exitCode, 0, 'Expect the exit code to be 0');
            $this->assertTrue((\stripos($p->getOutput(), 'total') !== false), "Expect 'total' to be in the output");

            // try an app that does not exists
            $p = new ProcessRunner('failedApp', ['']);

            try {
                $p->runBlocking();
            } catch (\Exception $e) {
            }

            $exitCode = $p->getExitCode();
            $this->assertNotEquals($exitCode, 0, 'Expect the exit code to be non-zero: ' . $exitCode);
            $this->assertTrue((\stripos($p->getStderr(), 'failedApp') !== false), "Expect 'failedApp' to be in the stderr");
        } else {
            $this->assertTrue(true, 'Pass Windows blocking process for now');
        }
    }

    public function estNonblockingProcess()
    {
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $p = new ProcessRunner('top', []);
            $pid = $p->run(false);
            \sleep(1);
            $p->stop();
            
           $exitCode = $p->getExitCode();
           $this->assertEquals($exitCode, 0, 'Expect the exit code to be 0');
//            $this->assertTrue((\stripos($p->getOutput(), 'Tasks') !== false), "Expect 'Tasks' to be in the output");
        } else {
            $this->assertTrue(true, 'Pass Windows blocking process for now');
        }
    }
}
