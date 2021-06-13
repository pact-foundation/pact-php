<?php

namespace PhpPact\Standalone\ProviderVerifier;

use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class VerifierTest extends TestCase
{
    /**
     * @throws NoDownloaderFoundException
     * @throws FileDownloadFailureException
     */
    public function testVerify(): void
    {
        $provider = new Process(['php', '-S', 'localhost:8000', '-t', __DIR__ . '/../../../_public']);
        $provider->start();
        $provider->waitUntil(function ($type, $output) {
            return false !== \strpos($output, 'Development Server (http://localhost:8000) started');
        });

        $config = new VerifierConfig();
        $config
            ->setPort(8000)
            ->setProviderName('someProvider')
            ->setProviderVersion('1.0.0')
            ->setProviderBranch('main');

        $verifier = new Verifier();
        $verifier->newHandle($config);
        $verifier->addDirectory(__DIR__ . '/../../../_resources');

        $this->assertTrue($verifier->verify());
    }
}
