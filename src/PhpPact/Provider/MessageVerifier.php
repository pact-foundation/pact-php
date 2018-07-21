<?php

namespace PhpPact\Provider;

use Amp\ByteStream\Payload;
use Amp\ByteStream\ResourceOutputStream;
use Amp\Http\Server\Request;

use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\Server;
use Amp\Http\Status;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Process\Process;
use GuzzleHttp\Psr7\Uri;
use Monolog\Logger;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use function Amp\Socket\listen;

class MessageVerifier extends Verifier
{
    /** @var callable */
    protected $callback;

    /**
     * MessageVerifier constructor.
     *
     * @param VerifierConfigInterface $config
     */
    public function __construct(VerifierConfigInterface $config)
    {
        // move this into a config
        $config->setProviderBaseUrl(new Uri('http://localhost:8080'));

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
     *
     * @throws \Exception
     */
    protected function verifyAction(array $arguments)
    {
        if (!$this->callback) {
            throw new \Exception('Callback needs to bet set when using message pacts');
        }

        $callback = $this->callback;
        $scripts = $this->installManager->install();

        $lambdaLoop = function () use ($callback, $scripts, $arguments) {

            // spin up a server

            // move this to configuration
            $servers = [
                listen('127.0.0.1:8080')
            ];

            // @todo migrate to Symphony logger
            $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT));
            $logHandler->setFormatter(new ConsoleFormatter);
            $logger = new Logger('server');
            $logger->pushHandler($logHandler);

            $server = new Server($servers, new CallableRequestHandler(function (Request $request) use ($callback) {
                $out = \call_user_func($callback);

                return new Response(Status::OK, [
                    'content-type' => 'application/json;',
                ], \json_encode($out));
            }), $logger);

            yield $server->start();

            // @todo move delay to config
            Loop::delay(3000, function () use ($scripts, $arguments){

                // "echo" is a shell internal command on Windows and doesn't work.
                $arguments = \array_merge([$scripts->getProviderVerifier()], $arguments);
                $cmd = \implode(' ', $arguments);
                $process = new Process($cmd);
                $process->start();

                $payload = new Payload($process->getStdout());
                print yield $payload->buffer();

                $code = yield $process->join();
                print "Process exited with {$code}.\n";

                Loop::stop();
            });
        };

        Loop::run($lambdaLoop);

//        $loop = EventLoop\Factory::create();
//        $callback = $this->callback;
//
//        $server = new Http\Server(function (ServerRequestInterface $request) use ($callback) {
//
//            echo "\n**** got a request \n";
//            error_log("\n**** got a request \n");
//
//            // handle the provider verifier response in the server
//            //$out = \call_user_func($callback);
//            $out = array("hi");
//
//            return new Http\Response(
//                200,
//                array('Content-Type' => 'application/json'),
//                \json_encode($out)
//            );
//        });
//
//        // move port to config
//        $socket = new Socket\Server(8080, $loop);
//        $server->listen($socket);
//
//        $scripts = $this->installManager->install();
//
//        // kick off the provider verifier by a timer
//        $loop->addTimer(5.0, function () use ($arguments, $scripts, $loop) {
//
//            // move to be configuration based, not env config
//            $arguments = \array_merge([$scripts->getProviderVerifier()], $arguments);
//            error_log("\n**** about to run provider \n");
//            error_log(print_r($arguments, true));
//
//            echo "\n**** about to run provider \n";
//
//            $process = new Process($arguments, null, null, null, $this->processTimeout);
//            $process->setIdleTimeout($this->processIdleTimeout);
//
//            $this->console->write("Verifying PACT with script {$process->getCommandLine()}");
//
//            $process->mustRun(function ($type, $buffer) {
//                $this->console->write("{$type} > {$buffer}");
//            });
//
//            /*
//            $xmlData = file_get_contents('http://localhost:8080');
//            error_log("Response: " . $xmlData);
//            */
//
//            $client = new GuzzleClient();
//            $o = $client->get(new Uri("http://localhost:8080"));
//
//            error_log("Response: " . (string) $o->getBody());
//            echo "\n**** ran provider \n";
//        });
//
//        // shut off the proxy server by timer with messages
//        // move 5 seconds to config
//        $loop->addTimer(8.0, function () use ($loop) {
//            echo "\n**** Stopping Proxy Server after 15 seconds\n";
//            $loop->stop();
//        });
//
//
//        error_log("Running loop");
//        echo "\n**** Running loop\n";
//        $loop->run();

        print "\n**** Stopping loop\n";

        // where do we check for errors?
    }
}
