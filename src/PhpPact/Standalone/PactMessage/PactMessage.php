<?php

namespace PhpPact\Standalone\PactMessage;

use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Runner\ProcessRunner;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
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

        $this->console->writeln("Starting the mock service with command {$this->process->getCommandLine()}.");

        $this->process->start(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->console->write($buffer);
            } else {
                $this->console->write($buffer);
            }
        });
        \sleep(1);

        $output = $this->process->getOutput();

        return $output;
    }

    public function update()
    {

    }

}