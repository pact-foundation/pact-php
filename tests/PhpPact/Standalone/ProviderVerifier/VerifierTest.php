<?php

namespace PhpPactTest\Standalone\ProviderVerifier;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPact\Standalone\Runner\ProcessRunner;
use PHPUnit\Framework\TestCase;

class VerifierTest extends TestCase
{
    /** @var ProcessRunner */
    private ProcessRunner $processRunner;

    /**
     * Run the PHP build-in web server.
     */
    protected function setUp(): void
    {
        $publicPath    =  __DIR__ . '/../../../_public/';

        $this->processRunner = new ProcessRunner('php', ['-S', 'localhost:7202', '-t', $publicPath]);

        $this->processRunner->run();
        \sleep(1); // wait for server to start
    }

    /**
     * Stop the web server process once complete.
     */
    protected function tearDown(): void
    {
        $this->processRunner->stop();
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
