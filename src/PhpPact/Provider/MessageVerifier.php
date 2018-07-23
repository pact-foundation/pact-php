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
     * Default host name for the proxy server
     *
     * @var string
     */
    protected $defaultProxyHost = 'localhost';

    /**
     * Default port for the proxy server to listen on
     *
     * @var int
     */
    protected $defaultProxyPort = 7201;

    /**
     * floor(provider-verification timeout / this value) = default verificationDelaySec
     *
     * @var int
     */
    protected $defaultDelayFactor = 3;

    /**
     * Set the number of seconds to delay the verification test to allow the proxy server to be stood up
     *
     * By default, it is a third of the provider-verification timeout
     *
     * @var float
     */
    protected $verificationDelaySec;


    /**
     * MessageVerifier constructor.
     *
     * @param VerifierConfigInterface $config
     */
    public function __construct(VerifierConfigInterface $config)
    {
        parent::__construct($config);

        $baseUrl = @$this->config->getProviderBaseUrl();
        if (!$baseUrl) {
            $config->setProviderBaseUrl(new Uri("http://{$this->defaultProxyHost}:{$this->defaultProxyPort}"));
        }

        // default verification delay
        $this->setVerificationDelaySec(floor($config->getProcessIdleTimeout() / $this->defaultDelayFactor));
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
     * @param float $verificationDelaySec
     * @return MessageVerifier
     */
    public function setVerificationDelaySec(float $verificationDelaySec): MessageVerifier
    {
        $this->verificationDelaySec = $verificationDelaySec;
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
        $uri = $this->config->getProviderBaseUrl();

        $scripts  = $this->installManager->install();
        $arguments = \array_merge([$scripts->getProviderVerifier()], $arguments);

        $lambdaLoop = function () use ($callback, $arguments, $uri) {
            // spin up a server
            $url = "{$uri->getHost()}:{$uri->getPort()}";
            $servers = [
                listen($url)
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


            // delay long enough for the server to be stood up
            $delay = intval($this->verificationDelaySec * 1000);

            // call the provider-verification cmd
            Loop::delay( $delay , function () use ($arguments) {
                $cmd = \implode(' ', $arguments);
                $process = new Process($cmd);
                $process->start();

                $payload = new Payload($process->getStdout());
                print yield $payload->buffer();

                $code = yield $process->join();

                // if the provider verification cmd returns a non-zero number, the test failed
                if ($code !== 0) {
                    throw new \Exception("Pact failed to validate.  Exit code: {$code}");
                }

                Loop::stop();
            });
        };

        Loop::run($lambdaLoop);
    }
}
