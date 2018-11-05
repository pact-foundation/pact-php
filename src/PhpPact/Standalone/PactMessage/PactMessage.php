<?php

namespace PhpPact\Standalone\PactMessage;

use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Runner\ProcessRunner;

class PactMessage
{
    /** @var InstallManager */
    private $installManager;

    /** @var Process */
    private $process;

    /** @var Filesystem */
    private $fileSystem;

    /** @var ConsoleOutput */
    private $console;

    public function __construct()
    {
        $this->installManager = new InstallManager();
    }

    /**
     * Build an example from the data structure back into its generated form
     * i.e. strip out all of the matchers etc
     *
     * @param Message $pact
     *
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     *
     * @return string
     */
    public function reify(Message $pact): string
    {
        $scripts = $this->installManager->install();

        $json    = \json_encode($pact);
        $process = new ProcessRunner($scripts->getPactMessage(), ['reify', "'" . $json . "'"]);

        $process->run($blocking = true);

        $output = $process->getOutput();
        \preg_replace("/\r|\n/", '', $output);

        return $output;
    }

    /**
     * Update a pact with the given message, or create the pact if it does not exist. The MESSAGE_JSON may be in the legacy Ruby JSON format or the v2+ format.
     *
     * @param string $pactJson
     * @param string $consumer
     * @param string $provider
     * @param string $pactDir
     *
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     *
     * @return bool
     */
    public function update(string $pactJson, string $consumer, string $provider, string $pactDir): bool
    {
        $scripts = $this->installManager->install();

        $arguments   = [];
        $arguments[] = 'update';
        $arguments[] = "--consumer={$consumer}";
        $arguments[] = "--provider={$provider}";
        $arguments[] = "--pact-dir={$pactDir}";
        $arguments[] = "'" . $pactJson . "'";

        $process = new ProcessRunner($scripts->getPactMessage(), $arguments);
        $process->run($blocking = true);

        \sleep(1);

        return true;
    }
}
