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
        $scripts  = $this->installManager->install();

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

                // @todo change status code based on errors on $out
                return new Response(Status::OK, [
                    'content-type' => 'application/json;',
                ], $out);
            }), $logger);

            yield $server->start();

            // @todo move delay to config
            Loop::delay(3000, function () use ($scripts, $arguments) {

                $arguments = \array_merge([$scripts->getProviderVerifier()], $arguments);
                $cmd = \implode(' ', $arguments);
                $process = new Process($cmd);
                $process->start();

                $payload = new Payload($process->getStdout());
                print yield $payload->buffer();

                $code = yield $process->join();

                print "Process exited with {$code}.\n";
                if ($code !== 0) {
                    throw new \Exception('Pact failed to validate');
                }
                Loop::stop();
            });
        };

        Loop::run($lambdaLoop);

        // where do we check for errors?
    }
}
