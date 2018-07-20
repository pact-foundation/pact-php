<?php

namespace PhpPact\Provider;

use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use Symfony\Component\Process\Process;
use React\EventLoop;
use React\Http;
use Psr\Http\Message\ServerRequestInterface;
use React\Socket;

class MessageVerifier extends Verifier
{
    /** @var callable */
    protected $callback;

    public function __construct(VerifierConfigInterface $config)
    {
        // move this into a config
        $config->setProviderBaseUrl('http://localhost:8080');

        parent::__construct($config);
    }

    /**
     * @param callable $callback
     *
     * @return self
     */
    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @param array $arguments
     * @throws \Exception
     */
    protected function verifyAction(array $arguments)
    {
        if (!$this->callback) {
            throw new \Exception("Callback needs to bet set when using message pacts");
        }

        // spin up a server
        $loop = EventLoop\Factory::create();
        $callback = $this->callback;

        $server = new Http\Server(function (ServerRequestInterface $request) use ($callback) {
            // handle the provider verifier response in the server
            $out = \call_user_func($callback);

            return new Http\Response(
                200,
                array('Content-Type' => 'application/json'),
                json_encode($out)
            );
        });

        // move port to config
        $socket = new Socket\Server(8080, $loop);
        $server->listen($socket);


        // kick off the provider verifier by a timer
        $loop->addTimer(1.0, function () use ($arguments) {
            // move to be configuration based, not env config
            $scripts = $this->installManager->install();

            $arguments = \array_merge([$scripts->getProviderVerifier()], $arguments);

            $process = new Process($arguments, null, null, null, $this->processTimeout);
            $process->setIdleTimeout($this->processIdleTimeout);

            $this->console->write("Verifying PACT with script {$process->getCommandLine()}");

            $process->mustRun(function ($type, $buffer) {
                $this->console->write("{$type} > {$buffer}");
            });
        });

        // shut off the proxy server by timer with messages
        // move 5 seconds to config
        $loop->addTimer(5.0, function () use ($loop) {
            echo "Stopping Proxy Server after 5 seconds";
            $loop->stop();
        });

        $loop->run();

        // where do we check for errors?
    }
}
