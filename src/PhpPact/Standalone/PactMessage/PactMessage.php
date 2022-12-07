<?php

namespace PhpPact\Standalone\PactMessage;

use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\Runner\ProcessRunner;

class PactMessage
{
    /**
     * Build an example from the data structure back into its generated form
     * i.e. strip out all of the matchers etc
     *
     * @param Message $pact
     *
     * @return string
     */
    public function reify(Message $pact): string
    {
        $json    = \json_encode($pact);
        $process = new ProcessRunner(Scripts::getPactMessage(), ['reify', "'" . $json . "'"]);

        $process->runBlocking();

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
     * @return bool
     */
    public function update(string $pactJson, string $consumer, string $provider, string $pactDir): bool
    {
        $arguments   = [];
        $arguments[] = 'update';
        $arguments[] = "--consumer={$consumer}";
        $arguments[] = "--provider={$provider}";
        $arguments[] = "--pact-dir={$pactDir}";
        $arguments[] = "'" . $pactJson . "'";

        $process = new ProcessRunner(Scripts::getPactMessage(), $arguments);
        $process->runBlocking();

        \sleep(1);

        return true;
    }
}
