<?php

namespace PhpPact\Standalone\PactMessage;

use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Runner\ProcessRunner;
use PhpPact\Standalone\PactConfigInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;


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
        $this->fileSystem     = new Filesystem();
        $this->console        = new ConsoleOutput();
    }


    /**
     * Build an example from the data structure back into its generated form
     * i.e. strip out all of the matchers etc
     *
     * @param Message $pact
     * @return string
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     */
    public function reify(Message $pact): string
    {
        $scripts = $this->installManager->install();

        $json = \json_encode($pact);
        $this->process = ProcessRunner::run($scripts->getPactMessage(), ["reify", $json] );
        $this->process
            ->setTimeout(600)
            ->setIdleTimeout(60);

        $this->console->writeln("Running the pact-message with command: {$this->process->getCommandLine()}");

        $this->process->start(function ($type, $buffer) {
            $this->console->write($buffer);
        });
        \sleep(1);

        $output = $this->process->getOutput();
        preg_replace( "/\r|\n/", "", $output );

        // add error handling if json is not returned
        return $output;
    }

    /**
     * Update a pact with the given message, or create the pact if it does not exist. The MESSAGE_JSON may be in the legacy Ruby JSON format or the v2+ format.
     *
     * @param string $pactJson
     * @param string $consumer
     * @param string $provider
     * @param string $pactDir
     * @return bool
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     */
    public function update(string $pactJson, string $consumer, string $provider, string $pactDir): bool
    {
        $scripts = $this->installManager->install();

        $arguments = [];
        $arguments[] = 'update';
        $arguments[] = $pactJson;
        $arguments[] = "--consumer={$consumer}";
        $arguments[] = "--provider={$provider}";
        $arguments[] = "--pact-dir={$pactDir}";

        $this->process = ProcessRunner::run($scripts->getPactMessage(), $arguments);
        $this->process
            ->setTimeout(600)
            ->setIdleTimeout(60);

        $this->console->writeln("Running the pact-message with command: {$this->process->getCommandLine()}");

        $this->process->start(function ($type, $buffer) {
            $this->console->write($buffer);
        });
        \sleep(1);

        return true;
    }
}