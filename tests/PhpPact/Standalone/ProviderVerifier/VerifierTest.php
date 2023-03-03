<?php

namespace PhpPactTest\Standalone\ProviderVerifier;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class VerifierTest extends TestCase
{
    /** @var Process */
    private Process $process;

    /**
     * Run the PHP build-in web server.
     */
    protected function setUp(): void
    {
        $publicPath    =  __DIR__ . '/../../../_public/';

        $this->process = new Process(['php', '-S', 'localhost:7202', '-t', $publicPath]);

        $this->process->start();
        $this->process->waitUntil(fn () => is_resource(fsockopen('localhost', 7202)));
    }

    /**
     * Stop the web server process once complete.
     */
    protected function tearDown(): void
    {
        $this->process->stop();
    }

    public function testVerify(): void
    {
        $config = new VerifierConfig();
        $config
            ->setPort(7202)
            ->setProviderName('someProvider')
            ->setProviderVersion('1.0.0')
            ->setProviderBranch('main');

        $verifier = new Verifier($config);
        $verifier->addDirectory(__DIR__ . '/../../../_resources');

        $this->assertTrue($verifier->verify());
    }
}
