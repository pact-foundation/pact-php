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

        $this->process = new Process(['php', '-S', '127.0.0.1:7202', '-t', $publicPath]);

        $this->process->start();
        $this->process->waitUntil(function (): bool {
            $fp = @fsockopen('127.0.0.1', 7202);
            $isOpen = is_resource($fp);
            if ($isOpen) {
                fclose($fp);
            }

            return $isOpen;
        });
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
        $config->getProviderInfo()
            ->setName('someProvider')
            ->setHost('localhost')
            ->setPort(7202)
            ->setScheme('http')
            ->setPath('/');
        if ($level = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($level);
        }

        $verifier = new Verifier($config);
        $verifier->addDirectory(__DIR__ . '/../../../_resources');

        $verifyResult = $verifier->verify();

        $this->assertTrue($verifyResult);
    }
}
