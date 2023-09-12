<?php

namespace JsonProvider\Tests;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PactVerifyTest extends TestCase
{
    private Process $process;

    /**
     * Run the PHP build-in web server.
     */
    protected function setUp(): void
    {
        $publicPath    =  __DIR__ . '/../public/';

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

    /**
     * This test will run after the web server is started.
     */
    public function testPactVerifyConsumer()
    {
        $config = new VerifierConfig();
        $config->getProviderInfo()
            ->setName('jsonProvider') // Providers name to fetch.
            ->setHost('localhost')
            ->setPort(7202);
        $config->getProviderState()
            ->setStateChangeUrl(new Uri('http://localhost:7202/pact-change-state'))
        ;
        if ($level = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($level);
        }

        $verifier = new Verifier($config);
        $verifier->addFile(__DIR__ . '/../../pacts/jsonConsumer-jsonProvider.json');

        $verifyResult = $verifier->verify();

        $this->assertTrue($verifyResult);
    }
}
